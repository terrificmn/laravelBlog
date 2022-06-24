<?php

namespace App\Providers;

use App\View\Composers\MetaComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers... // register MetaComposer 등록
        View::composer('layouts.app', MetaComposer::class);
    }
}
