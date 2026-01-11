<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MigrationRecord extends Model
{
    use HasFactory;

    protected $table = 'migration_records';

    protected $fillable = [
        'member_id',
        'user_id',
        'from_place',
        'to_place',
        'migration_date',
        'reason',
        'description'
    ];

    protected $dates = [
        'migration_date',
        'created_at',
        'updated_at'
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
