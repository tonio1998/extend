<?php

namespace App\Models\Dts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_actions';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'category',
        'color',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}
