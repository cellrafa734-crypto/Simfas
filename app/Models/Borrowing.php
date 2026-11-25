<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Borrowing extends Model {
    protected $fillable = ['user_id','facility_id','units','start_at','end_at','purpose','status','reject_reason'];
    protected $casts = ['start_at'=>'datetime','end_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
}
