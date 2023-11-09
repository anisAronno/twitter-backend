<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\TweetController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('/profile', 'userProfile');
});

Route::controller(HomeController::class)->middleware(['auth:api'])->group(function () {
    Route::get('home', 'homeTweets');
    Route::get('random-tweets', 'randomTweets');
    Route::get('tweets-by-username/{user:username}', 'tweetsByUsername');
});

Route::controller(TweetController::class)->middleware(['auth:api'])->group(function () {
    Route::get('tweet', 'index');
    Route::get('tweet/{tweet}', 'show');
    Route::post('tweet', 'store');
    Route::put('tweet/{tweet}', 'update');
    Route::delete('tweet/{tweet}', 'destroy');
});

Route::controller(FollowerController::class)->middleware(['auth:api'])->group(function () {
    Route::post('follow', 'follow');
    Route::post('unfollow', 'unFollow');
});

Route::controller(ReactionController::class)->middleware(['auth:api'])->group(function () {
    Route::post('react/{tweet}', 'reactToTweet');
    Route::post('remove-react/{tweet}', 'removeReactFromTweet');
});
