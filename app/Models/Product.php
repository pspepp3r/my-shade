<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 *
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property string|null $image_path
 *
 * @property User $user
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'image_path',
    ];

    protected $hidden = ['user_id'];

    protected function user()
    {
        return $this->belongsTo(User::class);
    }
}
