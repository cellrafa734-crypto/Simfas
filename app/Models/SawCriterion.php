<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SawCriterion extends Model {
    protected $table = 'saw_criteria';
    protected $fillable = ['name','weight','type'];
    public function scores() { return $this->hasMany(SawScore::class); }
}
