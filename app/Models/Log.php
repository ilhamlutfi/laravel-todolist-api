<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'event',
        'description'
    ];

    public static function record($user = null, $event, $description)
    {
        return static::create([
            'user_id' => $user->id,
            'ip' => request()->ip(),
            'event' => $event,
            'description' => $description
        ]);
    }
}
