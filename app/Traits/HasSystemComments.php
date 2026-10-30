<?php

namespace App\Traits;

use App\Models\SaaS\SystemComment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasSystemComments
{
    /**
     * Polymorphic relation to SystemComment.
     */
    public function systemComments(): MorphMany
    {
        return $this->morphMany(SystemComment::class, 'commentable')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Add a comment to this model in the centralized sys_comments table.
     */
    public function addComment(string $comment, string $actionType = 'comment', array $options = []): SystemComment
    {
        $user = Auth::user();
        $tenantId = $options['tenant_id'] ?? ($this->tenant_id ?? ($user?->tenant_id ?? 1));

        return $this->systemComments()->create([
            'tenant_id' => $tenantId,
            'user_id' => $options['user_id'] ?? $user?->id,
            'author_name' => $options['author_name'] ?? null,
            'author_designation' => $options['author_designation'] ?? null,
            'action_type' => $actionType,
            'comment' => $comment,
            'rating_at_time' => $options['rating_at_time'] ?? null,
            'stage_at_time' => $options['stage_at_time'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    /**
     * Get count of comments on this model.
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->systemComments()->count();
    }
}
