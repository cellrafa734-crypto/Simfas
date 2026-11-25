<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SawScore extends Model {
    protected $fillable = ['facility_id','saw_criterion_id','value'];
    public function facility() { return $this->belongsTo(Facility::class); }
    public function criterion() { return $this->belongsTo(SawCriterion::class, 'saw_criterion_id'); }
}
