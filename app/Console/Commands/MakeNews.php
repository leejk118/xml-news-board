<?php

namespace App\Console\Commands;

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

        $dirs = scandir($newsDir);

        foreach ($dirs as $dateDir) {

            if (is_dir($dateDir)) continue;

            $articles = scandir($newsDir . "/" . $dateDir);

            foreach ($articles as $article){
                $ext = pathinfo($article);
                if($ext['extension'] != 'xml') continue;

                $xmlfile = $newsDir . "/" . $dateDir . "/" . $article;
                $xml = simplexml_load_file($xmlfile) or die("Error!!");

                $sendDate = $xml->Header->SendDate;
                $taggedBody = $xml->NewsContent->TaggedBody;

                $imgPath = $this->getImgPath($sendDate);
                $taggedBody = $this->replaceXmlBody($taggedBody, $imgPath);

                $this->saveNewsImg($xml->NewsContent->AppendData, $imgPath, $sendDate);
                $this->insertDB($xml, $taggedBody, $sendDate);

                break;
            }
        }

        return 0;
    }

    public function getImgPath(string $date){
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);

        return "news_img/" . $year . "/" . $month . "/" . $day . "/";
    }

    public function replaceXmlBody($taggedBody, $imgPath){
        $taggedBody = str_replace("\n", '<br/>', $taggedBody);

        $pattern = '/<YNAPHOTO(.+?)\/>/';
        $result = preg_replace_callback($pattern, function ($matches) use($imgPath){
            preg_match("/path='(.*?)'/", $matches[1], $path);
            preg_match("/title='(.*?)'/", $matches[1], $title);
            preg_match("/caption='(.*?)'/", $matches[1], $caption);

            $result = "<img src='/" . $imgPath . $path[1] . "' />";
            if (isset($title[1])) $result .= "<br><strong>" . $title[1] . "</strong>";
            if (isset($caption[1])) $result .= "<p>" .$caption[1] . "</p>";

            return $result;
        }, $taggedBody);

        return $result;
    }

    public function saveNewsImg($imgs, $imgPath, $sendDate){
        foreach ($imgs as $img){
            if (!file_exists("public/" . $imgPath)){
                mkdir("public/" . $imgPath, 0777, true);
            }

            $this->searchFile($img->FileName, $sendDate);
        }
    }

    public function searchFile($filename, $sendDate) {
        $dateDir = substr($sendDate, 0, 4) . "-" . substr($sendDate, 4, 2)
            . "-" . substr($sendDate, 6, 2);

        $newsDir = public_path() . "/news/" . $dateDir;

        $files = scandir($newsDir);

        foreach ($files as $file){
            if ($file == $filename){
                copy($newsDir . "/" . $file, public_path() . "/" . $this->getImgPath($sendDate) . $filename);
                echo $file . " copied\n";
                break;
            }
        }
    }

    public function insertDB($xml, $taggedBody, $sendDate){
        \App\Article::create([
            'title' => $xml->NewsContent->Title,
            'subtitle' => $xml->NewsContent->SubTitle,
            'content' => $taggedBody,
            'send_date' => $sendDate,
            'news_link' =>$xml->Metadata->Href
        ]);
    }
}
