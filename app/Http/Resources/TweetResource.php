<?php

namespace App\Http\Resources;

use App\Enums\React;
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
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            "total_reactions" => $this->whenNotNull($this->total_reactions),
            "user_reactions" => $this->whenNotNull($this->user_reactions),
            "reaction_count" => $this->whenNotNull($this->reaction_count),
            "reaction_arr" => React::values(),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
