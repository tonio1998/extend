<?php

namespace App\Models\SaaS;

/**
 * Backward-compatible wrapper around the centralized SystemComment model.
 */
class DocumentComment extends SystemComment
{
    protected $table = 'sys_comments';

    /**
     * Map document_type to commentable_type and document_id to commentable_id.
     */
    public function setDocumentTypeAttribute($value): void
    {
        $this->attributes['commentable_type'] = $value;
    }

    public function getDocumentTypeAttribute(): ?string
    {
        return $this->attributes['commentable_type'] ?? null;
    }

    public function setDocumentIdAttribute($value): void
    {
        $this->attributes['commentable_id'] = $value;
    }

    public function getDocumentIdAttribute(): ?int
    {
        return $this->attributes['commentable_id'] ?? null;
    }
}
