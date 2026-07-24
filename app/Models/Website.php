<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = ['client_id', 'name', 'url', 'technology', 'hosting_provider', 'domain_expires_at', 'hosting_expires_at', 'ssl_status', 'maintenance_plan', 'status', 'notes'];

    protected function casts(): array
    {
        return ['domain_expires_at' => 'date', 'hosting_expires_at' => 'date'];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function maintenanceTasks()
    {
        return $this->hasMany(MaintenanceTask::class);
    }
}
