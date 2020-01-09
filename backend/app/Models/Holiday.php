<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'date', 'description'
    ];

    public function getList(Carbon $startDate): array
    {
        return Holiday::whereBetween('date', [
                            $startDate->format('Y').'-01-01',
                            $startDate->format('Y').'-12-31'
                        ])
                        ->pluck('date')
                        ->toArray();
    }
}
