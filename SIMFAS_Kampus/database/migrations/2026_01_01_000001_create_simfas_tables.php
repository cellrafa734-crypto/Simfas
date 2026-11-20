<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', fn(Blueprint $t) => $t->string('role', 20)->default('mahasiswa')->after('email')->index());
        Schema::create('facilities', function (Blueprint $t) {
            $t->id(); $t->string('code',30)->unique(); $t->string('name',150); $t->string('category',60)->index();
            $t->text('description')->nullable(); $t->string('location',150); $t->unsignedInteger('units')->default(1);
            $t->enum('status',['tersedia','dipinjam','pemeliharaan','tidak_dapat_digunakan'])->default('tersedia');
            $t->enum('condition',['baik','rusak_ringan','rusak_sedang','rusak_berat'])->default('baik');
            $t->timestamps();
        });
        Schema::create('borrowings', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('facility_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('units')->default(1); $t->dateTime('start_at'); $t->dateTime('end_at');
            $t->string('purpose'); $t->string('reject_reason')->nullable();
            $t->enum('status',['menunggu','disetujui','ditolak','sedang_digunakan','selesai','dibatalkan'])->default('menunggu');
            $t->timestamps(); $t->index(['facility_id','start_at','end_at']);
        });
        Schema::create('saw_criteria', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->decimal('weight',5,4); $t->enum('type',['benefit','cost']); $t->timestamps();
        });
        Schema::create('saw_scores', function (Blueprint $t) {
            $t->id(); $t->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $t->foreignId('saw_criterion_id')->constrained('saw_criteria')->cascadeOnDelete();
            $t->decimal('value',8,2); $t->timestamps(); $t->unique(['facility_id','saw_criterion_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('saw_scores'); Schema::dropIfExists('saw_criteria'); Schema::dropIfExists('borrowings'); Schema::dropIfExists('facilities');
        Schema::table('users', fn(Blueprint $t) => $t->dropColumn('role'));
    }
};
