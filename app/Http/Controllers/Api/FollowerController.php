<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFollowerRequest;
use App\Models\Follower;

class FollowerController extends Controller
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
     * Store a newly created resource in storage.
     */
    public function follow(StoreFollowerRequest $request)
    {
        $followerId = auth()->user()->id;
        $followingId = $request->input('following_id');

        if (!Follower::where(['follower_id' => $followerId, 'following_id' => $followingId])->exists()) {
            Follower::create(['follower_id' => $followerId, 'following_id' => $followingId]);

            return response()->json([
                'message' => 'User followed successfully.',
                'success' => true,
            ]);
        }

        return response()->json([
            'message' => 'User is already followed.',
            'success' => false,
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */

    public function unFollow(StoreFollowerRequest $request)
    {
        $user = auth()->user();
        $followingId = $request->input('following_id');

        if ($user->following()->where('following_id', $followingId)->exists()) {
            $user->following()->detach($followingId);

            return response()->json([
                'message' => 'Unfollowed successfully.',
                'success' => true,
            ]);
        }

        return response()->json([
            'message' => 'You are not following this user.',
            'success' => false,
        ], 422);
    }

}
