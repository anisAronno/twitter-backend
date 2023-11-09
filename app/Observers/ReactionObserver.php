<?php

namespace App\Observers;

use App\Models\Reaction;
use Illuminate\Support\Facades\Cache;

class ReactionObserver
{
    /**
     * Handle the Reaction "created" event.
     */
    public function created(Reaction $reaction): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Reaction "updated" event.
     */
    public function updated(Reaction $reaction): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Reaction "deleted" event.
     */
    public function deleted(Reaction $reaction): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Reaction "restored" event.
     */
    public function restored(Reaction $reaction): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }

    /**
     * Handle the Reaction "force deleted" event.
     */
    public function forceDeleted(Reaction $reaction): void
    {
        Cache::tags(['tweet','followingTweets',  'tweetByUserName'])->flush();
    }
}
