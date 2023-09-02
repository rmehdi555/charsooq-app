<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleindexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array(
            'category' => $this->category->name,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'is_show' => $this->is_show,
            'file caption' => $this->thumbnail->caption,
            'file path' => $this->thumbnail->path,
            'created_by' => $this->author->name,
        );
    }
}
