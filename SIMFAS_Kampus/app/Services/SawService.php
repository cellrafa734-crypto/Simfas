<?php
namespace App\Services;
use App\Models\{Facility, SawCriterion, SawScore};
class SawService {
    /** Mengembalikan matriks keputusan X, matriks normalisasi R, dan ranking V = sum(w*r). */
    public function compute(): array {
        $criteria = SawCriterion::orderBy('id')->get();
        $facilities = Facility::whereHas('scores')->get();
        $X = [];
        foreach ($facilities as $f) foreach ($criteria as $c)
            $X[$f->id][$c->id] = (float) (SawScore::where(['facility_id'=>$f->id,'saw_criterion_id'=>$c->id])->value('value') ?? 0);
        $R = []; $V = [];
        foreach ($facilities as $f) {
            $V[$f->id] = 0;
            foreach ($criteria as $c) {
                $col = array_column($X, $c->id);
                $x = $X[$f->id][$c->id];
                $R[$f->id][$c->id] = $c->type === 'benefit'
                    ? (max($col) > 0 ? $x / max($col) : 0)
                    : ($x > 0 ? min($col) / $x : 0);
                $V[$f->id] += $c->weight * $R[$f->id][$c->id];
            }
        }
        arsort($V);
        return compact('criteria','facilities','X','R','V');
    }
}
