<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "image" => $this->image,
            "username" => $this->username,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'tweets' => TweetResource::collection($this->whenLoaded('tweets')),
            'followers' => FollowerResource::collection($this->whenLoaded('followers')),
            'following' => FollowingResource::collection($this->whenLoaded('following')),
        ];
    }
}
