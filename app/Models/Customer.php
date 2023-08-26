<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [] ;



    public function modal() {
        return $this->hasOne(Brand::class, 'id', 'modal_id');  
    }

    public function color() {
        return $this->hasOne(Color::class, 'id', 'color_id');  
    }

    
    public function fault() {
        return $this->hasOne(Fault::class, 'id', 'fault_id');  
    }

}
