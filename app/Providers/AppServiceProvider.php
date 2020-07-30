<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        view()->composer("*", function($view){
            $newsHistories = Cache::remember('newsHistories', "" , function(){
                return \App\NewsHistory::where('send_date', '=', '2020-07-16')->
                                with('article')->orderBy('id', 'desc')->limit(5)->get();
            });

            $view->with(compact('newsHistories'));
        });
    }
}
