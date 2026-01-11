<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AncestralHall extends Model
{
    use HasFactory;

    protected $table = 'ancestral_halls';

    protected $fillable = [
        'name',
        'location',
        'built_date',
        'description',
        'clan_id',
        'user_id'
    ];

    protected $dates = [
        'built_date',
        'created_at',
        'updated_at'
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
