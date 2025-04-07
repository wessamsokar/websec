<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // Disable timestamps
    public $timestamps = false;

    // Define the fillable fields
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'total_price',
        'purchased_at',
    ];

    // Define the relationship with the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
