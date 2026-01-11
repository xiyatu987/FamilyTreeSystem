<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grave extends Model
{
    use HasFactory;

    protected $table = 'graves';

    protected $fillable = [
        'location',
        'description',
        'member_id',
        'user_id'
    ];

    // 与用户的关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 与家族成员的关联
    public function member()
    {
        return $this->belongsTo(FamilyMember::class, 'member_id');
    }
}
