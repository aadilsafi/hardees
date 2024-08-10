<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'tblstores';
    public $timestamps = false;


    public function scheduleApprovals()
    {
        return $this->hasMany(ScheduleApproval::class, 'UnitNo', 'StoreNumber');
    }

}
