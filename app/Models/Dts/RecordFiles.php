<?php

namespace App\Models\Dts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecordFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_files';

    protected $fillable = [
        'tenant_id',
        'RecordID',
        'filename',
        'location',
        'size',
        'extension',
        'type',
        'qrcoded_file',
        'qrcoded_file_temp',
        'description',
        'status',
        'archived',
        'locked',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'archived' => 'integer',
        'locked' => 'integer',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(Records::class, 'RecordID');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
