<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\SawCriterion;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes kecil untuk halaman SAW (bobot, nilai, dan tampilan).
 * Rumus di SawService tidak diuji/diubah di sini.
 */
class SawPageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_halaman_saw_tampil_walau_data_masih_kosong(): void
    {
        $this->actingAs($this->admin())
            ->get('/saw')
            ->assertOk()
            ->assertSee('Belum ada hasil perankingan');
    }

    public function test_halaman_saw_menampilkan_ranking_dari_data_seeder(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs($this->admin())
            ->get('/saw')
            ->assertOk()
            ->assertSee('Matriks Keputusan (X)')
            ->assertSee('Matriks Normalisasi (R)')
            ->assertSee('Aula Utama')
            ->assertSee('Sound System');
    }

    public function test_bobot_dengan_total_satu_berhasil_disimpan(): void
    {
        $this->seed(DatabaseSeeder::class);
        $ids = SawCriterion::orderBy('id')->pluck('id')->all();

        $weights = [
            $ids[0] => 0.40,
            $ids[1] => 0.30,
            $ids[2] => 0.20,
            $ids[3] => 0.10,
        ];

        $this->actingAs($this->admin())
            ->post('/saw/weights', ['weights' => $weights])
            ->assertSessionHas('ok');

        $this->assertEqualsWithDelta(0.40, (float) SawCriterion::find($ids[0])->weight, 0.0001);
    }

    public function test_bobot_dengan_total_bukan_satu_ditolak(): void
    {
        $this->seed(DatabaseSeeder::class);
        $ids = SawCriterion::orderBy('id')->pluck('id')->all();
        $sebelum = (float) SawCriterion::find($ids[0])->weight;

        $this->actingAs($this->admin())
            ->post('/saw/weights', ['weights' => [
                $ids[0] => 0.50,
                $ids[1] => 0.30,
                $ids[2] => 0.20,
                $ids[3] => 0.10,
            ]])
            ->assertSessionHas('err');

        $this->assertEqualsWithDelta($sebelum, (float) SawCriterion::find($ids[0])->weight, 0.0001);
    }

    public function test_nilai_di_luar_rentang_satu_sampai_lima_ditolak(): void
    {
        $this->seed(DatabaseSeeder::class);
        $facility = Facility::orderBy('id')->first();
        $values = SawCriterion::orderBy('id')->pluck('id')->mapWithKeys(fn ($id) => [$id => 9])->all();

        $this->actingAs($this->admin())
            ->post('/saw/score', ['facility_id' => $facility->id, 'values' => $values])
            ->assertSessionHasErrors();
    }

    public function test_penilaian_yang_tidak_lengkap_ditolak(): void
    {
        $this->seed(DatabaseSeeder::class);
        $facility = Facility::orderBy('id')->first();
        $ids = SawCriterion::orderBy('id')->pluck('id')->all();

        // Hanya mengisi satu kriteria dari empat.
        $this->actingAs($this->admin())
            ->post('/saw/score', ['facility_id' => $facility->id, 'values' => [$ids[0] => 3]])
            ->assertSessionHas('err');
    }
}
