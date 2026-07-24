<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['company_name', 'contact_name', 'email', 'phone', 'city', 'status', 'notes'];

    public function websites()
    {
        return $this->hasMany(Website::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function reports()
    {
        return $this->hasMany(MonthlyReport::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
