<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'location',
        'participants',
        'description',
        'clan_id',
        'user_id'
    ];

    // 与宗族的关联
    public function clan()
    {
        return $this->belongsTo(Clan::class);
    }

    // 与用户的关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
