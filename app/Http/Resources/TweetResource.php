<?php

namespace App\Http\Resources;

use App\Http\Resources\ReactionResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TweetResource extends JsonResource
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
            "content" => $this->content,
            "reactions_count" => $this->whenNotNull($this->reactions_count),
            'user' => new UserResource($this->whenLoaded('user')),
            'reactions' => ReactionResource::collection($this->whenLoaded('reactions')),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
