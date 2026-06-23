<?php

namespace App\Infrastructure\Link\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLinkModel extends Model
{
    protected $table = 'short_links';

    protected $fillable = [
        'slug',
        'destination_url',
        'title',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClickModel::class, 'short_link_id');
    }
}
