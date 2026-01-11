<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyRule extends Model
{
    use HasFactory;

    protected $table = 'family_rules';

    protected $fillable = [
        'title',
        'content',
        'category',
        'clan_id',
        'user_id'
    ];

    // 与用户的关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 与宗族的关联
    public function clan()
    {
        return $this->belongsTo(Clan::class);
    }
}
