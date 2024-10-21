<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'id',
        'name',
        'company',
        'contact',
        'country',
        'email',
        'status_id'
    ];

    /**
     * Relationship: A customer can have multiple addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Relationship: A customer can belong to many projects
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class,'customer_projects');
    }
}
