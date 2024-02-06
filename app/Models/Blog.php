<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public function catagory(){
        return $this->belongsTo(Category::class,"category_id");
    }

    public function image(){
        return $this->hasOne(Image::class);
    }

    public $fillable = [
        'title',
        'description',
        // 'image',
        'category_id'
    ];

    public function scopeFilter($query , $filter){

    }
}
