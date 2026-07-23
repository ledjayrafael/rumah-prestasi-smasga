<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->enum('category', ['akademik', 'non_akademik', 'organisasi']);
            $table->enum('level', ['sekolah', 'kabupaten', 'provinsi', 'nasional', 'internasional']);
            $table->enum('participation_type', ['perorangan', 'tim']);
            $table->string('rank_label')->comment('mis. Juara 1, Juara Harapan 1, Peserta');
            $table->string('organizer');
            $table->date('event_date');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'revision'])->default('pending');
            $table->text('reviewer_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
