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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('meeting_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('location');
            $table->date('next_meeting_date')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed'])->default('scheduled');
            $table->foreignId('chairperson_id')->constrained('users');
            $table->foreignId('secretary_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->text('raw_notes')->nullable();
            $table->text('ai_summary')->nullable();
            $table->string('ai_summary_status')->nullable();
            $table->timestamp('ai_summary_generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
