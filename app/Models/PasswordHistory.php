<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'password_hash'
    ];

    protected $dates = ['created_at'];

    public $timestamps = false;

    /**
     * User who owns this password history
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for recent passwords
     */
    public function scopeRecent($query, int $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}