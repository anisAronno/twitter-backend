<?php

namespace App\Http\Controllers\Api;

use App\Enums\React;
use App\Http\Controllers\Controller;
use App\Http\Resources\TweetResource;
use App\Models\Tweet;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeController extends Controller
{
    /**
    * Create a new AuthController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
 * Get tweets from users that the authenticated user is following.
 */
    public function homeTweets(): JsonResource
    {
        $user = auth()->user();

        $tweets = Tweet::whereIn('user_id', $user->following()->pluck('users.id'))
            ->with(['user', 'reactions.user'])
            ->orderByDesc('id')
            ->get();

        return TweetResource::collection($tweets)
            ->additional([
                'message' => 'Home tweets retrieved successfully.',
                'success' => true,
            ]);
    }

    /**
     * Show tweets from users that the authenticated user is following.
     *
     * @return JsonResource
     */
    public function randomTweets($randomBy = 'id'): JsonResource
    {
        $user = auth()->user();

        $tweets = Tweet::select('tweets.*')
            ->leftJoin('reactions', 'tweets.id', '=', 'reactions.tweet_id')
            ->whereIn('tweets.user_id', $user->following()->pluck('users.id'))
            ->withCount('reactions')
            ->groupBy('tweets.id')
            ->orderByRaw('
            CASE WHEN ? = "id" THEN RAND() ELSE tweets.created_at END,
            reactions_count DESC,
            MAX(CASE WHEN reactions.react = ? THEN 1 ELSE 0 END) DESC
        ', [$randomBy, React::LOVE])
            ->get();

        return TweetResource::collection($tweets->load(['user', 'reactions.user']))
            ->additional([
                'message' => 'Following tweets retrieved successfully.',
                'success' => true,
        ]);
    }

    /**
     * Get tweets by username.
     *
     * @param  string  $username
     * @return JsonResource
    */
    public function tweetsByUsername($username): JsonResource
    {
        $tweets = Tweet::whereHas('user', function ($query) use ($username) {
            $query->where('username', $username);
        })
        ->with(['user', 'reactions.user'])
        ->orderByDesc('id')
        ->get();

        return TweetResource::collection($tweets)
            ->additional([
                'message' => 'Tweets retrieved successfully.',
                'success' => true,
        ]);
    }

}
