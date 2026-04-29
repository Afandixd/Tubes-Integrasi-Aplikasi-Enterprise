<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];
    protected $hidden = ['price', 'created_at', 'updated_at'];
}
