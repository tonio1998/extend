<?php

namespace App\Models\SaaS;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SystemComment extends Model
{
    use HasFactory;

    protected $table = 'sys_comments';

    protected $guarded = ['id'];

    protected $casts = [
        'rating_at_time' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'author_name',
        'author_designation',
        'author_initials',
        'created_at_human',
        'created_at_formatted',
    ];

    /**
     * Polymorphic parent model relation.
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User who authored the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tenant owning this comment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Author display name accessor.
     */
    public function getAuthorNameAttribute(): string
    {
        if (! empty($this->attributes['author_name'])) {
            return $this->attributes['author_name'];
        }

        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->employee?->full_name ?: $this->user->name;
        }

        if ($this->user_id) {
            $user = User::with('employee')->find($this->user_id);

            return $user?->employee?->full_name ?: ($user?->name ?: 'Authorized Signatory');
        }

        return 'System User';
    }

    /**
     * Author official designation accessor.
     */
    public function getAuthorDesignationAttribute(): string
    {
        if (! empty($this->attributes['author_designation'])) {
            return $this->attributes['author_designation'];
        }

        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->employee?->designation ?: ($this->user->designation ?: 'Staff');
        }

        if ($this->user_id) {
            $user = User::with('employee')->find($this->user_id);

            return $user?->employee?->designation ?: ($user?->designation ?: 'Staff');
        }

        return 'Staff';
    }

    /**
     * 2-letter avatar initials accessor.
     */
    public function getAuthorInitialsAttribute(): string
    {
        $name = $this->author_name;
        $parts = preg_split('/\s+/', trim($name));

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr(end($parts), 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Relative human timestamp.
     */
    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * Formatted date string.
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y h:i A') : '';
    }

    /**
     * Scope to filter comments for a given transaction type and ID.
     * Supports type aliases (e.g. 'extension_plan', 'memo', 'policy', 'dts', 'accreditation_benchmark')
     * or full Eloquent model class names.
     */
    public function scopeForTransaction(Builder $query, string $type, int|string $id): Builder
    {
        return $query->where('commentable_type', $type)
            ->where('commentable_id', $id);
    }

    /**
     * Scope for oldest first.
     */
    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for newest first.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }
}
