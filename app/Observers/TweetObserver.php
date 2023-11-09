<?php

namespace App\Observers;

use App\Models\Tweet;
use Cache;

class TweetObserver
{
    /**
     * Handle the Tweet "created" event.
     */
    public function created(Tweet $tweet): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Tweet "updated" event.
     */
    public function updated(Tweet $tweet): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Tweet "deleted" event.
     */
    public function deleted(Tweet $tweet): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Tweet "restored" event.
     */
    public function restored(Tweet $tweet): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Tweet "force deleted" event.
     */
    public function forceDeleted(Tweet $tweet): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }
}
