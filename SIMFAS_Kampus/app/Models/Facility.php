<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Facility extends Model {
    protected $fillable = ['code','name','category','description','location','units','status','condition'];
    public function borrowings() { return $this->hasMany(Borrowing::class); }
    public function scores() { return $this->hasMany(SawScore::class); }
}
