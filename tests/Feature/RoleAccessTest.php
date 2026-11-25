<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan pembatasan akses per role (halaman yang harus menghasilkan 403).
 * Pengecekan 403 terjadi di middleware, jadi tidak bergantung pada isi tampilan.
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userDenganRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/saw')->assertRedirect('/login');
        $this->get('/borrowings')->assertRedirect('/login');
    }

    public function test_petugas_tidak_boleh_membuka_peminjaman_dan_saw(): void
    {
        $petugas = $this->userDenganRole('petugas');

        $this->actingAs($petugas)->get('/borrowings')->assertForbidden();
        $this->actingAs($petugas)->get('/borrowings/create')->assertForbidden();
        $this->actingAs($petugas)->get('/saw')->assertForbidden();
        $this->actingAs($petugas)->get('/facilities/create')->assertForbidden();
    }

    public function test_mahasiswa_dan_organisasi_tidak_boleh_membuka_halaman_admin(): void
    {
        foreach (['mahasiswa', 'organisasi'] as $role) {
            $user = $this->userDenganRole($role);

            $this->actingAs($user)->get('/saw')->assertForbidden();
            $this->actingAs($user)->get('/facilities/create')->assertForbidden();
            $this->actingAs($user)->post('/saw/weights', [])->assertForbidden();
            $this->actingAs($user)->post('/saw/score', [])->assertForbidden();
        }
    }

    public function test_admin_tidak_boleh_membuka_form_pengajuan_peminjaman(): void
    {
        $admin = $this->userDenganRole('admin');

        $this->actingAs($admin)->get('/borrowings/create')->assertForbidden();
    }
}
