<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clan extends Model
{
    use HasFactory;

    protected $table = 'clans';

    protected $fillable = [
        'name',
        'founding_date',
        'ancestral_home',
        'description',
        'user_id'
    ];

    protected $dates = [
        'founding_date',
        'created_at',
        'updated_at'
    ];

    // 与用户的关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 与祠堂的关联
    public function ancestralHalls()
    {
        return $this->hasMany(AncestralHall::class);
    }
}
