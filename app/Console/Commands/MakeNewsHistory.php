<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeNewsHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:make-news-history';

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
        echo "make-news-history : " . now() . "\n";

        $tempDate = "2020-07-17";
        \App\NewsHistory::where('send_date', '=', $tempDate)->delete();


        $articles = \App\Article::select('id', 'send_date', 'view_count')
                                ->whereRaw('send_date = SUBDATE("20200718", 1)')
                                ->orderBy('view_count', 'desc')
                                ->limit(5)
                                ->get();

        foreach ($articles as $article) {
            \App\NewsHistory::create(['send_date' => $article->send_date,
                                    'article_id' => $article->id,
                                    'view_count' => $article->view_count]);
        }

        echo $articles . "\n";
        return 0;
    }
}
