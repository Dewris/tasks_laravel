<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TaskScope;

class Task extends Model
{
    use HasFactory;
    public const STATUS_TODO = 'todo';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'user_id',
        'parent_id',
        'title',
        'description',
        'priority',
        'status',
        'closed_at',
    ];

    public function subtask(){
        return $this->hasMany(Task::class,'parent_id');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new TaskScope);
    }
}
