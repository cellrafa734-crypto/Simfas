<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    private function rules(?int $id = null): array
    {
        return [
            'code' => 'required|max:30|unique:facilities,code,' . $id,
            'name' => 'required|max:150',
            'category' => 'required|max:60',
            'description' => 'nullable',
            'location' => 'required|max:150',
            'units' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,dipinjam,pemeliharaan,tidak_dapat_digunakan',
            'condition' => 'required|in:baik,rusak_ringan,rusak_sedang,rusak_berat',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }

    public function index()
    {
        return view('facilities.index', [
            'items' => Facility::orderBy('name')->paginate(15)
        ]);
    }

    public function create()
    {
        return view('facilities.form', [
            'f' => new Facility
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());

        if ($r->hasFile('image')) {
            $data['image_path'] = $r->file('image')
                ->store('facilities', 'public');
        }

        Facility::create($data);

        return redirect()
            ->route('facilities.index')
            ->with('ok', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility)
    {
        return view('facilities.form', [
            'f' => $facility
        ]);
    }

    public function update(Request $r, Facility $facility)
    {
        $data = $r->validate($this->rules($facility->id));

        if ($r->hasFile('image')) {
            $newImage = $r->file('image')
                ->store('facilities', 'public');

            if ($facility->image_path) {
                Storage::disk('public')->delete($facility->image_path);
            }

            $data['image_path'] = $newImage;
        }

        $facility->update($data);

        return redirect()
            ->route('facilities.index')
            ->with('ok', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->borrowings()->exists()) {
            return back()->with(
                'err',
                'Fasilitas memiliki riwayat peminjaman, tidak dapat dihapus.'
            );
        }

        if ($facility->image_path) {
            Storage::disk('public')->delete($facility->image_path);
        }

        $facility->delete();

        return back()->with('ok', 'Fasilitas berhasil dihapus.');
    }
}