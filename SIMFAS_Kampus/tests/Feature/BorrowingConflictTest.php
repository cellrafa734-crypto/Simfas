<?php
namespace Tests\Feature;
use App\Models\{Facility, Borrowing, User};
use App\Services\BorrowingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class BorrowingConflictTest extends TestCase {
    use RefreshDatabase;
    public function test_overlap_reduces_availability_and_non_overlap_does_not(): void {
        $u = User::factory()->create(); $f = Facility::create(['code'=>'X1','name'=>'Aula','category'=>'Ruangan','location'=>'A','units'=>1]);
        Borrowing::create(['user_id'=>$u->id,'facility_id'=>$f->id,'units'=>1,'start_at'=>'2030-01-01 08:00','end_at'=>'2030-01-01 10:00','purpose'=>'t','status'=>'disetujui']);
        $s = new BorrowingService;
        $this->assertSame(0, $s->available($f,'2030-01-01 09:00','2030-01-01 11:00'));
        $this->assertSame(1, $s->available($f,'2030-01-01 10:00','2030-01-01 12:00'));
    }
    public function test_student_cannot_open_admin_page(): void {
        $u = User::factory()->create(['role'=>'mahasiswa']);
        $this->actingAs($u)->get('/saw')->assertForbidden();
    }
}
