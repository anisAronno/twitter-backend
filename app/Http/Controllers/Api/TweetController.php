<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Http\Resources\TweetResource;
use App\Models\Tweet;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TweetController extends Controller
{
    /**
    * Create a new AuthController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->authorizeResource(Tweet::class, 'tweet');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $tweets = auth()->user()->tweets()->orderByDesc('id')->get();

        return (TweetResource::collection($tweets->load(['reactions.user'])))->additional([
            'message'  => 'Tweet retrieved successfully.',
            'success'  => true,
         ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTweetRequest $request)
    {
        try {
            DB::beginTransaction();

            $tweet = auth()->user()->tweets()->create([
                'content' => $request->input('content'),
            ]);

            DB::commit();
            return (new TweetResource($tweet->load(['reactions.user'])))->additional([
                'message' => 'Tweet created successfully.',
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Tweet $tweet)
    {
        return (new TweetResource($tweet->load(['reactions.user'])))->additional([
            'message' => 'Tweet retrieved successfully.',
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTweetRequest $request, Tweet $tweet)
    {
        try {
            DB::beginTransaction();

            auth()->user()->tweets()->update([
                'content' => $request->input('content'),
            ]);

            DB::commit();
            return (new TweetResource($tweet->load(['reactions.user'])))->additional([
                'message' => 'Tweet updated successfully.',
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet)
    {
        if ($tweet->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully deleted?!'
            ], 400);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Deleted Failed!'
            ], 400);
        }
    }
}
