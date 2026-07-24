<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $fillable = ['client_id', 'month', 'year', 'summary', 'completed_tasks_count', 'resolved_tickets_count', 'pending_tickets_count', 'recommendations', 'general_status'];

    protected function casts(): array
    {
        return ['month' => 'integer', 'year' => 'integer'];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
