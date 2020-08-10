<?php

namespace App\Providers;

use App\NewsHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 테스트용 날짜 데이터
     * @var string
     */
    protected $tempDate = '2020-07-17';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('articles.index', function ($view) {
            $newsHistories = Cache::remember('newsHistories', "", function () {
                return NewsHistory::where('send_date', '=', $this->tempDate)
                                ->with('article')
                                ->orderBy('view_count', 'desc')
                                ->get();
            });

            $view->with(compact('newsHistories'));
        });
    }
}
