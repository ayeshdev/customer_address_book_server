<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

     // Fillable fields
     protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relationship: A project can have many customers
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class,'customer_projects');
    }
}
