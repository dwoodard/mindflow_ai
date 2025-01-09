<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'input',
        'output',
        'status',
        'pattern', // E.g., 'PromptChain', 'Router'
    ];

    // public function workflow()
    // {
    //     return $this->belongsTo(Workflow::class);
    // }
}
