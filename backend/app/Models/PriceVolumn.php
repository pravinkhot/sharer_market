<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceVolumn extends Model
{
	protected $table = 'price_volumn';
	
    protected $fillable = [
    	'symbol','series','open','high','low','close','last','prev_close','total_traded_quantity','total_traded_value','traded_at','total_trades','isin'
    ];
}
