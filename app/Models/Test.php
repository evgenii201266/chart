<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\Parser\TimeFormatHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use TimeFormatHelper;
    
    protected $table = 'test';
}
