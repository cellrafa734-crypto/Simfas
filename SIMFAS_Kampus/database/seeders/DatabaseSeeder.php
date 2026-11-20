<?php
namespace Database\Seeders;
use App\Models\{User, Facility, SawCriterion, SawScore};
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        foreach (['admin'=>'Admin SIMFAS','mahasiswa'=>'Mahasiswa Demo','organisasi'=>'BEM Demo','petugas'=>'Petugas Demo'] as $role=>$name)
            User::updateOrCreate(['email'=>"$role@simfas.test"], ['name'=>$name,'password'=>'password123','role'=>$role]);
        $fs = [['R-001','Aula Utama','Ruangan','Gedung A',1,'baik'],['R-002','Lab Komputer 1','Ruangan','Gedung B',1,'rusak_ringan'],
               ['P-001','Proyektor','Peralatan','Gudang',5,'rusak_sedang'],['P-002','Sound System','Peralatan','Gudang',2,'rusak_berat']];
        foreach ($fs as [$c,$n,$k,$l,$u,$cond]) Facility::updateOrCreate(['code'=>$c], ['name'=>$n,'category'=>$k,'location'=>$l,'units'=>$u,'condition'=>$cond]);
        $crit = [['Tingkat kerusakan',0.35,'benefit'],['Dampak akademik',0.30,'benefit'],['Urgensi',0.25,'benefit'],['Biaya perbaikan',0.10,'cost']];
        foreach ($crit as [$n,$w,$t]) SawCriterion::updateOrCreate(['name'=>$n], ['weight'=>$w,'type'=>$t]);
        $vals = [[2,5,3,2],[3,4,4,3],[3,3,3,2],[5,2,4,5]];
        foreach (Facility::orderBy('id')->get() as $i=>$f) foreach (SawCriterion::orderBy('id')->get() as $j=>$c)
            SawScore::updateOrCreate(['facility_id'=>$f->id,'saw_criterion_id'=>$c->id], ['value'=>$vals[$i][$j]]);
    }
}
