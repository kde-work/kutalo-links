<?php

namespace App\Infrastructure\Link\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkClickModel extends Model
{
    public $timestamps = false;

    protected $table = 'link_clicks';

    protected $fillable = [
        'short_link_id',
        'clicked_at',
        'ip',
        'user_agent',
        'referer',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function shortLink(): BelongsTo
    {
        return $this->belongsTo(ShortLinkModel::class, 'short_link_id');
    }
}
