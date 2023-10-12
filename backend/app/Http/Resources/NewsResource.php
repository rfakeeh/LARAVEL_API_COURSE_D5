<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $author = (new UserResource($this->user))->toArray($request);

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'thumbnail' => $this->resource->thumbnail,
            'visible' => $this->resource->visible,
            'completed' => $this->resource->completed,
            'updated_at' => $this->resource->updated_at,
            'body' => $this->resource->body,
            'album_size' => $this->resource->images()->count(),
            'album' => ImageResource::collection($this->images),
            'categories_count' => $this->resource->categories()->distinct()->get()->count(),
            'categories' => CategoryResource::collection($this->resource->categories()->distinct()->get()),
            'author_id' => $author['id'],
            'author_avatar' => $author['avatar'],
            //'author_name' => $this->resource->user->first_name . ' ' . $this->resource->user->last_name,
            'author_name' => $author['name']
            
        ];
    }
}
