<?php

namespace App\Providers;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider{


public function boot(): void
{
    View::composer('site.*', function ($view) {
        $view->with('navCategories', Category::orderBy('id')->get());
    });
}
}
