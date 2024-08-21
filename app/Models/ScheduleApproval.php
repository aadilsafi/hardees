<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleApproval extends Model
{
    use HasFactory;
    protected $table = 'tblScheduleApproval';
    public $timestamps = false;
    protected $fillable = ['Approved', 'ApprovedBy','Comments'];
    protected $primaryKey = 'ID';

    public function store()
    {
        return $this->belongsTo(Store::class, 'UnitNo', 'StoreNumber');
    }
}
