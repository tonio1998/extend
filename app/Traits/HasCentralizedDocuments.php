<?php

namespace App\Traits;

use App\Models\SaaS\DocumentAuditTrail;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\UploadedDocument;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasCentralizedDocuments
{
    /**
     * Centralized uploaded attachments & physically signed files.
     */
    public function uploadedDocuments(): MorphMany
    {
        return $this->morphMany(UploadedDocument::class, 'documentable')->latest();
    }

    /**
     * Primary active physically signed document scan.
     */
    public function physicallySignedDocument(): MorphOne
    {
        return $this->morphOne(UploadedDocument::class, 'documentable')
            ->where('is_physically_signed', true)
            ->where('status', 'active')
            ->latestOfMany();
    }

    /**
     * Centralized audit trail logs for all document actions on this model.
     */
    public function documentAuditTrails(): MorphMany
    {
        return $this->morphMany(DocumentAuditTrail::class, 'documentable')->latest();
    }

    /**
     * Centralized signatories attached to this document.
     */
    public function documentSignatories(): MorphMany
    {
        return $this->morphMany(DocumentSignatory::class, 'document', 'document_type', 'document_id')
            ->orderBy('position_column')
            ->orderBy('sort_order');
    }
}
