<?php

namespace App\Http\Resources;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            "title" => $this->title,
            "description" => $this->description,
            "category" => Category::findOrFail($this->category_id)->title,
            "category_id" => $this->category_id,
            "created_blog" => Carbon::parse($this->created_at)->diffForHumans(),
            "image" => $this->image->image ,
            "extension" => $this->image->extension,
            "pagination" => $this->currentPage
        ];
    }
}
