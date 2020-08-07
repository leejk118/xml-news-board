<?php

namespace App\Console\Commands;

use App\Exceptions\ImageNotFoundException;
use Illuminate\Console\Command;

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

    public $imgPath;

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
        $newsDir = public_path() . "/news";
        $newsDateDirs = scandir($newsDir);

        foreach ($newsDateDirs as $newsDateDir) {
            $newsPathDir = $newsDir . "/" . $newsDateDir;
            if (!is_dir($newsPathDir) || $newsDateDir == '.' || $newsDateDir == '..') {
                continue;
            }

            $articlesXml = scandir($newsPathDir);
            foreach ($articlesXml as $articleXml) {
                $ext = pathinfo($articleXml);
                if ($ext['extension'] != 'xml') {
                    continue;
                }

                $xml = simplexml_load_file($newsPathDir . "/" . $articleXml);
                if ($xml === false){
                    echo "Failed Loading XML\n";
                    foreach (libxml_get_errors() as $error){
                        echo $error->message . "\t";
                    }
                    continue;
                }

                $newsContent = $xml->NewsContent;
                $this->imgPath = $this->getImgPath($xml->Header->SendDate);

                $taggedBody = $this->getTaggedBody($newsContent->TaggedBody);
                $preview_img = $this->getPreviewImg($newsContent->AppendData->FileName);
                $preview_content = $this->getPreviewContent($newsContent->Body);

                $this->saveNewsImg($newsContent->AppendData, $xml->Header->SendDate);
                $this->insertDB($xml, $taggedBody, $preview_img, $preview_content);

                break;
            }
        }

        return 0;
    }

    public function getPreviewImg($prevImg)
    {
        return (isset($prevImg)) ? $this->imgPath . $prevImg : null;
    }

    public function getPreviewContent($body)
    {
        return iconv_substr(str_replace("\n", ' ', $body), 0, 100, "UTF-8");
    }

    public function getImgPath(string $date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);

        return "news_img/" . $year . "/" . $month . "/" . $day . "/";
    }

    public function getTaggedBody($taggedBody)
    {
        $taggedBody = str_replace("\n", '<br/><br/>', $taggedBody);

        $pattern = '/<YNAPHOTO(.+?)\/>/';
        $result = preg_replace_callback($pattern, function ($matches) {
            preg_match("/path='(.*?)'/", $matches[1], $path);
            preg_match("/title='(.*?)'/", $matches[1], $title);
            preg_match("/caption='(.*?)'/", $matches[1], $caption);

            $result = "<img src='/" . $this->imgPath . $path[1] . "' />";
            $result .= (isset($title[1])) ? "<br><strong>" . $title[1] . "</strong>" : "";
            $result .= (isset($caption[1])) ? "<p>" .$caption[1] . "</p>" : "";

            return $result;
        }, $taggedBody);

        return $result;
    }

    public function saveNewsImg($imgs, $sendDate)
    {
        foreach ($imgs as $img) {
            if (!file_exists("public/" . $this->imgPath)) {
                mkdir("public/" . $this->imgPath, 0777, true);
            }
            $this->searchFile($img->FileName, $sendDate);
        }
    }

    public function searchFile($filename, $sendDate)
    {
        $isExist = false;
        $dateDir = substr($sendDate, 0, 4) . "-" . substr($sendDate, 4, 2)
            . "-" . substr($sendDate, 6, 2);
        $newsDir = public_path() . "/news/" . $dateDir;

        $files = scandir($newsDir);

        try {
            foreach ($files as $file) {
                if ($file == $filename) {
                    copy($newsDir . "/" . $file, public_path() . "/" .
                        $this->getImgPath($sendDate) . $filename);
                    echo $file . " copied\n";
                    $isExist = true;
                    break;
                }
            }
            if (!$isExist) {
                throw new ImageNotFoundException("이미지 파일이 없습니다 : (" . $filename . ")\n");
            }
        }
        catch (ImageNotFoundException $exception){
            echo $exception->getMessage();
        }

    }

    public function insertDB($xml, $taggedBody, $preview_img, $preview_content)
    {
        try {
            \App\Article::create([
                'title' => $xml->NewsContent->Title,
                'subtitle' => $xml->NewsContent->SubTitle,
                'content' => $taggedBody,
                'send_date' => $xml->Header->SendDate,
                'news_link' => $xml->Metadata->Href,
                'preview_img' => $preview_img,
                'preview_content' => $preview_content
            ]);
        }
        catch(\Illuminate\Database\QueryException $exception){
            echo "Article Data가 생성되지 않음\n";
        }

    }
}
