<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCurrentSpending()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        
        if ($this->period === 'yearly') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        }

        return Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function getProgress()
    {
        $spending = $this->getCurrentSpending();
        return min(100, ($spending / $this->amount) * 100);
    }
}
