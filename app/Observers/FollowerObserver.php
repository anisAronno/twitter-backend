<?php

namespace App\Observers;

use App\Models\Follower;
use Illuminate\Support\Facades\Cache;

class FollowerObserver
{
    /**
     * Handle the Follower "created" event.
     */
    public function created(Follower $follower): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Follower "updated" event.
     */
    public function updated(Follower $follower): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Follower "deleted" event.
     */
    public function deleted(Follower $follower): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Follower "restored" event.
     */
    public function restored(Follower $follower): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Follower "force deleted" event.
     */
    public function forceDeleted(Follower $follower): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }
}
