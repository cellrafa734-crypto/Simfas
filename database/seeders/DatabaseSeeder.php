<?php
namespace Database\Seeders;
use App\Models\{User, Facility, SawCriterion, SawScore};
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        foreach (['admin'=>'Admin SIMFAS','mahasiswa'=>'Mahasiswa Demo','organisasi'=>'BEM Demo','petugas'=>'Petugas Demo'] as $role=>$name)
            User::firstOrCreate(
                ['email' => "$role@simfas.test"],
                ['name' => $name, 'password' => 'password123', 'role' => $role]
            );
        $fs = [['R-001','Aula Utama','Ruangan','Gedung A',1,'baik'],['R-002','Lab Komputer 1','Ruangan','Gedung B',1,'rusak_ringan'],
               ['P-001','Proyektor','Peralatan','Gudang',5,'rusak_sedang'],['P-002','Sound System','Peralatan','Gudang',2,'rusak_berat'],
               ['R-003','Ruang Seminar','Ruangan','Gedung C',1,'baik'],['R-004','Ruang Rapat','Ruangan','Gedung D',1,'baik'],
               ['P-003','Laptop','Peralatan','Gudang',3,'rusak_ringan'],['P-004','Kamera Dokumentasi','Peralatan','Gudang',2,'baik']];
        foreach ($fs as [$c,$n,$k,$l,$u,$cond]) Facility::updateOrCreate(['code'=>$c], ['name'=>$n,'category'=>$k,'location'=>$l,'units'=>$u,'condition'=>$cond]);
        $crit = [['Tingkat kerusakan',0.35,'benefit'],['Dampak akademik',0.30,'benefit'],['Urgensi',0.25,'benefit'],['Biaya perbaikan',0.10,'cost']];
        foreach ($crit as [$n,$w,$t]) SawCriterion::updateOrCreate(['name'=>$n], ['weight'=>$w,'type'=>$t]);
        // Urutan nilai mengikuti urutan kriteria di $crit: kerusakan, dampak akademik, urgensi, biaya.
        $vals = [
            'R-001'=>[2,5,3,2], 'R-002'=>[3,4,4,3], 'P-001'=>[3,3,3,2], 'P-002'=>[5,2,4,5],
            'R-003'=>[2,4,3,2], 'R-004'=>[2,3,2,2], 'P-003'=>[3,4,3,3], 'P-004'=>[2,2,2,2],
        ];
        $critNames = array_column($crit, 0);
        foreach ($vals as $code => $row) {
            $f = Facility::where('code', $code)->firstOrFail();
            foreach ($row as $j => $v) {
                $c = SawCriterion::where('name', $critNames[$j])->firstOrFail();
                SawScore::updateOrCreate(['facility_id'=>$f->id,'saw_criterion_id'=>$c->id], ['value'=>$v]);
            }
        }
    }
}
