<?php

declare(strict_types=1);

namespace App\Http\Resources;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $product_id
 *
 * @property string|null $content
//  * @property string|null $image_path
 *
 * @property Carbon $updated_at
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            // 'image_url' => asset("storage/{$this->image_path}"),
            'product_id' => $this->product_id,
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
