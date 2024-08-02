<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CategoryProduct extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable=[
        'name',
        'description',
        'status',
        'creator_id'
    ];

    public function getStatusAttribute(){
       return $this->attributes['status']==0?'Disabled':'Enabled';
    }
    // public function setStatusAttribute(){
    //    return $this->attributes['status']==false?0:1;
    // }
    
    
}
