<?php
namespace App\Http\Controllers;
use App\Models\Facility;
use Illuminate\Http\Request;
class FacilityController extends Controller {
    private function rules(?int $id = null): array {
        return ['code'=>'required|max:30|unique:facilities,code,'.$id,'name'=>'required|max:150','category'=>'required|max:60',
            'description'=>'nullable','location'=>'required|max:150','units'=>'required|integer|min:1',
            'status'=>'required|in:tersedia,dipinjam,pemeliharaan,tidak_dapat_digunakan',
            'condition'=>'required|in:baik,rusak_ringan,rusak_sedang,rusak_berat'];
    }
    public function index() { return view('facilities.index', ['items'=>Facility::orderBy('name')->paginate(15)]); }
    public function create() { return view('facilities.form', ['f'=>new Facility]); }
    public function store(Request $r) { Facility::create($r->validate($this->rules())); return redirect()->route('facilities.index')->with('ok','Fasilitas ditambahkan.'); }
    public function edit(Facility $facility) { return view('facilities.form', ['f'=>$facility]); }
    public function update(Request $r, Facility $facility) { $facility->update($r->validate($this->rules($facility->id))); return redirect()->route('facilities.index')->with('ok','Fasilitas diperbarui.'); }
    public function destroy(Facility $facility) {
        if ($facility->borrowings()->exists()) return back()->with('err','Fasilitas memiliki riwayat peminjaman, tidak dapat dihapus.');
        $facility->delete(); return back()->with('ok','Fasilitas dihapus.');
    }
}
