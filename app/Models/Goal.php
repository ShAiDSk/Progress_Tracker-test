<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_value',
        'current_value',
        'unit',
        'category',
        'priority',
        'status',
        'completed_at',
        'deadline',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'completed_at' => 'datetime',
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the goal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active goals only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed goals only
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for goals by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for goals by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for overdue goals
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->where('status', '!=', 'completed');
    }

    /**
     * Calculate progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_value <= 0) {
            return 0;
        }

        $percentage = ($this->current_value / $this->target_value) * 100;
        return min(100, max(0, round($percentage, 2)));
    }

    /**
     * Check if goal is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' ||
            $this->current_value >= $this->target_value;
    }

    /**
     * Check if goal is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->deadline || $this->status === 'completed') {
            return false;
        }

        return $this->deadline->isPast();
    }

    /**
     * Get days remaining until deadline
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Get formatted unit display
     */
    public function getFormattedUnitAttribute(): string
    {
        $units = [
            'days' => 'day(s)',
            'hours' => 'hour(s)',
            'minutes' => 'minute(s)',
            'reps' => 'rep(s)',
            'pages' => 'page(s)',
            'kilometers' => 'km',
            'miles' => 'mile(s)',
            'pounds' => 'lb',
            'kilograms' => 'kg',
            'count' => 'time(s)',
        ];

        return $units[$this->unit] ?? $this->unit;
    }

    /**
     * Increment goal progress
     */
    public function incrementProgress(float $amount = 1): bool
    {
        $newValue = $this->current_value + $amount;

        // Cap at target value
        if ($newValue > $this->target_value) {
            $newValue = $this->target_value;
        }

        $this->current_value = $newValue;

        // Auto-complete if target reached
        if ($newValue >= $this->target_value && $this->status === 'active') {
            $this->status = 'completed';
            $this->completed_at = now();
        }

        return $this->save();
    }

    /**
     * Mark goal as complete
     */
    public function markAsComplete(): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->current_value = $this->target_value;

        return $this->save();
    }

    /**
     * Reopen a completed goal
     */
    public function reopen(): bool
    {
        $this->status = 'active';
        $this->completed_at = null;

        return $this->save();
    }




    /**
     * Archive the goal
     */
    public function archive(): bool
    {
        $this->status = 'archived';
        return $this->save();
    }
}
