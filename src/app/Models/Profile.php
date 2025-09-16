<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'avatar_path', 'postal_code', 'address_line1', 'address_line2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
