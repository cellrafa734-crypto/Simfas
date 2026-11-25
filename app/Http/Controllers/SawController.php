<?php
namespace App\Http\Controllers;
use App\Models\{SawCriterion, SawScore, Facility};
use App\Services\SawService;
use Illuminate\Http\Request;
class SawController extends Controller {
    public function index(SawService $svc) {
        $res = $svc->compute();
        $res['sum'] = round(SawCriterion::sum('weight'), 4);
        $res['all'] = Facility::orderBy('name')->get();
        return view('saw.index', $res);
    }
    public function weights(Request $r) {
        $w = $r->validate(['weights'=>'required|array','weights.*'=>'numeric|min:0|max:1'])['weights'];
        $validIds = SawCriterion::whereIn('id', array_keys($w))->pluck('id')->map(fn($id)=>(string)$id)->all();
        if (count($validIds) !== count($w) || count($w) !== SawCriterion::count()) return back()->with('err','Data bobot kriteria tidak lengkap atau tidak valid.');
        if (abs(array_sum($w) - 1) > 0.0001) return back()->with('err','Total bobot harus 1 (100%). Saat ini: '.array_sum($w));
        foreach ($w as $id => $v) SawCriterion::whereKey($id)->update(['weight'=>$v]);
        return back()->with('ok','Bobot disimpan.');
    }
    public function score(Request $r) {
        $d = $r->validate(['facility_id'=>'required|exists:facilities,id','values'=>'required|array','values.*'=>'numeric|min:1|max:5']);
        $validIds = SawCriterion::whereIn('id', array_keys($d['values']))->pluck('id')->map(fn($id)=>(string)$id)->all();
        if (count($validIds) !== count($d['values']) || count($d['values']) !== SawCriterion::count()) return back()->with('err','Nilai untuk semua kriteria wajib diisi dengan benar.');
        foreach ($d['values'] as $cid => $v)
            SawScore::updateOrCreate(['facility_id'=>$d['facility_id'],'saw_criterion_id'=>$cid], ['value'=>$v]);
        return back()->with('ok','Penilaian disimpan.');
    }
}
