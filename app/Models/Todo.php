<?php

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  
        'title',
        'description',
        'completed',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'priority' => Priority::class,
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    // Scopes for filtering
    public function scopeOverdue(Builder $query): void
    {
        $query->where('due_date', '<', Carbon::today())
            ->where('completed', false);
    }

    public function scopeDueToday(Builder $query): void
    {
        $query->whereDate('due_date', Carbon::today())
            ->where('completed', false);
    }

    public function scopeDueSoon(Builder $query, int $days = 7): void
    {
        $query->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays($days)])
            ->where('completed', false);
    }

    public function scopeByPriority(Builder $query, Priority $priority): void
    {
        $query->where('priority', $priority);
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->completed;
    }

    public function isDueToday(): bool
    {
        return $this->due_date
            && $this->due_date->isToday()
            && !$this->completed;
    }
}
