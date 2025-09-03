<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'filename', 'path', 'mime_type', 'size',
    ];

    protected $appends = [
        'path_url',
        'is_image',
    ];

    public function getPathUrlAttribute(): string
    {
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        return Storage::url($this->path);
    }

    public function getIsImageAttribute(): bool
    {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $ext = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        return in_array($ext, $allowed_extensions, true);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function (Media $media) {
            Storage::delete($media->path);
        });
    }

    public function mediaable(): MorphTo
    {
        return $this->morphTo();
    }
}
