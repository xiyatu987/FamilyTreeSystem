<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ziwei extends Model
{
    use HasFactory;

    protected $table = 'ziwei';

    protected $fillable = [
        'character',
        'order',
        'generation',
        'description',
        'user_id'
    ];

    // 与用户的关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 与家族成员的关联
    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }
}