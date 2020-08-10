<?php

namespace App\Console\Commands;

use App\Exceptions\ImageNotFoundException;
use App\Exceptions\XmlParsingException;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class MakeNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:make-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * 날짜별 뉴스 디렉토리가 담겨있는 news 디렉토리
     *
     * @var string
     */
    protected $newsDirectory = 'public/news';

    /**
     * xml 파싱된 객체
     *
     * @var object
     */
    protected $articleXML;

    protected $imgPath;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $newsDateDirs = scandir($this->newsDirectory);
        foreach ($newsDateDirs as $newsDateDir) {
            $newsPathDir = $this->newsDirectory . "/" . $newsDateDir;
            if (!is_dir($newsPathDir) || $newsDateDir == '.' || $newsDateDir == '..') {
                continue;
            }

            $files = scandir($newsPathDir);
            foreach ($files as $file) {
                if ($this->isXmlFile($file) && $file != '.' && $file != '..') {
                    try {
                        $this->setArticleXml($newsPathDir . "/" . $file);
                        $this->setNewsImgPath($this->articleXML->Header->SendDate);
                        $this->saveNewsImg($this->articleXML->NewsContent->AppendData, $this->articleXML->Header->SendDate);
                        $this->insertArticleDB();
                    } catch (XmlParsingException | QueryException | ImageNotFoundException $e){
                        echo $e->getMessage();
                    }
                }
            }
        }
        return 0;
    }

    /**
     * xml 파일이면 true, 아니면 false 리턴
     *
     * @param $file
     * @return bool
     */
    public function isXmlFile($file) : bool
    {
        return pathinfo($file, PATHINFO_EXTENSION) == 'xml';
    }

    /**
     * XML파일 파싱하여 articleXML변수에 할당
     *
     * @param $xmlFile
     * @throws XmlParsingException
     */
    public function setArticleXml($xmlFile) : void
    {
        $this->articleXML = @simplexml_load_file($xmlFile);

        throw_unless($this->articleXML, new XmlParsingException("XML Parsing Error!! : $xmlFile\n"));
    }

    /**
     * newsImgPath를 news_img/0000/00/00/ 형식으로 설정
     *
     * @param $sendDate
     * @return void
     */
    public function setNewsImgPath($sendDate) : void
    {
        $year = substr($sendDate, 0, 4);
        $month = substr($sendDate, 4, 2);
        $day = substr($sendDate, 6, 2);

        $this->imgPath = "news_img/" . $year . "/" . $month . "/" . $day . "/";
    }

    /**
     * 미리보기 이미지의 경로를 지정, 이미지 없을 경우 null
     *
     * @param $prevImg
     * @return string|null
     */
    public function getPreviewImg($prevImg)
    {
        return (isset($prevImg)) ? ($this->imgPath . $prevImg) : null;
    }

    /**
     * 미리보기 본문 내용 리턴
     *
     * @param $body
     * @return string
     *
     * TODO : iconv_substr false 리턴 시 exception 처리
     */
    public function getPreviewContent($body) : string
    {
        return iconv_substr(str_replace("\n", ' ', $body), 0, 100, "UTF-8");
    }

    /**
     * 정규표현식을 통해 TaggedBody 내용 변환
     *
     * @param $taggedBody
     * @return string|string[]|null
     */
    public function getContent($taggedBody)
    {
        $taggedBody = str_replace("\n", '<br/><br/>', $taggedBody);

        $pattern = '/<YNAPHOTO(.+?)\/>/';
        $result = preg_replace_callback($pattern, function ($matches) {
            preg_match("/path='(.*?)'/", $matches[1], $path);
            preg_match("/title='(.*?)'/", $matches[1], $title);
            preg_match("/caption='(.*?)'/", $matches[1], $caption);

            $ret = "<img src='/" . $this->imgPath . $path[1] . "' />";
            $ret .= (isset($title[1])) ? "<br><strong>" . $title[1] . "</strong>" : "";
            $ret .= (isset($caption[1])) ? "<p>" .$caption[1] . "</p>" : "";

            return $ret;
        }, $taggedBody);

        return $result;
    }

    /**
     * 이미지 파일 저장
     *
     * @param $imgs
     * @param $sendDate
     * @throws ImageNotFoundException
     */
    public function saveNewsImg($imgs, $sendDate)
    {
        foreach ($imgs as $img) {
            if (!file_exists("public/" . $this->imgPath)) {
                mkdir("public/" . $this->imgPath, 0777, true);
            }
            throw_unless($this->searchFile($img->FileName, $sendDate),
                new ImageNotFoundException("이미지 파일이 없습니다!\n"));
        }
    }

    /**
     * 기존 폴더에서 검색하여 이미지 파일 저장
     *
     * @param $filename
     * @param $sendDate
     * @return bool
     */
    public function searchFile($filename, $sendDate) : bool
    {
        $isExist = false;
        $dateDir = substr($sendDate, 0, 4) . "-" . substr($sendDate, 4, 2)
            . "-" . substr($sendDate, 6, 2);
        $newsDir = $this->newsDirectory . '/' . $dateDir;

        $files = scandir($newsDir);

        foreach ($files as $file) {
            if ($file == $filename) {
                copy($newsDir . "/" . $file, public_path() . "/" .
                    $this->imgPath . $filename);
                echo $file . " copied\n";
                $isExist = true;
                break;
            }
        }

        return $isExist;
    }

    /**
     * DB에 적재
     *
     * @return void
     * @throws QueryException
     */
    public function insertArticleDB()
    {
        try {
            \App\Article::create([
                'title' => $this->articleXML->NewsContent->Title,
                'subtitle' => $this->articleXML->NewsContent->SubTitle,
                'content' => $this->getContent($this->articleXML->NewsContent->TaggedBody),
                'send_date' => $this->articleXML->Header->SendDate,
                'news_link' => $this->articleXML->Metadata->Href,
                'preview_img' => $this->getPreviewImg($this->articleXML->NewsContent->AppendData->FileName),
                'preview_content' => $this->getPreviewContent($this->articleXML->NewsContent->Body)
            ]);
        }
        catch(\Illuminate\Database\QueryException $exception){
            echo "Insert Database Error!!\n";
        }
    }
}
