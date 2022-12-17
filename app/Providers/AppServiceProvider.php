<?php

namespace App\Providers;

use App\Models\CommentLab;
use App\Models\CommentRad;
use App\Models\CommentRecipe;
use App\Models\CommentRehab;
use App\Models\Recipe;
use App\Models\RequestLab;
use App\Models\RequestRad;
use App\Models\RequestRehab;
use App\Observers\CommentLabObserver;
use App\Observers\CommentRadObserver;
use App\Observers\CommentRecipeObserver;
use App\Observers\CommentRehabObserver;
use App\Observers\CreateLabObserver;
use App\Observers\CreateRadObserver;
use App\Observers\CreateRecipeObserver;
use App\Observers\CreateRehabObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
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
        Carbon::setLocale('id');

        Schema::defaultStringLength(191);

        RequestLab::observe(CreateLabObserver::class);

        RequestRad::observe(CreateRadObserver::class);

        CommentRad::observe(CommentRadObserver::class);

        Recipe::observe(CreateRecipeObserver::class);

        CommentLab::observe(CommentLabObserver::class);

        CommentRecipe::observe(CommentRecipeObserver::class);

        RequestRehab::observe(CreateRehabObserver::class);

        CommentRehab::observe(CommentRehabObserver::class);
    }
}
