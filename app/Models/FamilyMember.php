<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';

    protected $fillable = [
        'name',
        'gender',
        'birth_date',
        'death_date',
        'birth_place',
        'death_place',
        'father_id',
        'mother_id',
        'spouse_id',
        'ziwei_id',
        'generation',
        'description',
        'user_id'
    ];

    protected $dates = [
        'birth_date',
        'death_date',
        'created_at',
        'updated_at'
    ];

    // 与用户的关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 与字辈的关联
    public function ziwei()
    {
        return $this->belongsTo(Ziwei::class);
    }

    // 父亲关联
    public function father()
    {
        return $this->belongsTo(self::class, 'father_id');
    }

    // 母亲关联
    public function mother()
    {
        return $this->belongsTo(self::class, 'mother_id');
    }

    // 配偶关联
    public function spouse()
    {
        return $this->belongsTo(self::class, 'spouse_id');
    }

    // 子女关联
    public function children()
    {
        return self::where(function($query) {
            $query->where('father_id', $this->id)
                  ->orWhere('mother_id', $this->id);
        })->where('user_id', $this->user_id);
    }

    // 与祠堂的关联
    public function ancestralHall()
    {
        return $this->belongsTo(AncestralHall::class);
    }

    // 与迁徙记录的关联
    public function migrations()
    {
        return $this->hasMany(MigrationRecord::class, 'member_id');
    }

    // 与墓地信息的关联
    public function grave()
    {
        return $this->hasOne(Grave::class);
    }

    // 获取性别中文显示
    public function getGenderDisplayAttribute()
    {
        $genderMap = [
            'male' => '男',
            'female' => '女',
            'other' => '其他'
        ];
        return $genderMap[$this->gender] ?? $this->gender;
    }

    // 获取是否在世
    public function getIsAliveAttribute()
    {
        return is_null($this->death_date);
    }
}