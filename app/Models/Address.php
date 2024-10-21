<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

        // Fillable fields
        protected $fillable = [
            'id',
            'customer_id',
            'no',
            'street',
            'city',
            'state',
        ];

        /**
         * Relationship: An address belongs to a customer
         */
        public function customer()
        {
            return $this->belongsTo(Customer::class);
        }
}
