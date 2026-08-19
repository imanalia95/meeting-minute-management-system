<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: widen the enum so both old and new values are valid at once —
        // required before we can safely rewrite existing rows
        DB::statement("ALTER TABLE meetings MODIFY status ENUM('scheduled','ongoing','completed','draft','final','approved') NOT NULL DEFAULT 'scheduled'");

        // Step 2: map old meaning to new meaning
        DB::table('meetings')->where('status', 'completed')->update(['status' => 'final']);
        DB::table('meetings')->whereIn('status', ['scheduled', 'ongoing'])->update(['status' => 'draft']);

        // Step 3: narrow the enum down to just the new set
        DB::statement("ALTER TABLE meetings MODIFY status ENUM('draft','final','approved') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE meetings MODIFY status ENUM('draft','final','approved','scheduled','ongoing','completed') NOT NULL DEFAULT 'draft'");

        DB::table('meetings')->where('status', 'final')->update(['status' => 'completed']);
        DB::table('meetings')->where('status', 'approved')->update(['status' => 'completed']);
        DB::table('meetings')->where('status', 'draft')->update(['status' => 'scheduled']);

        DB::statement("ALTER TABLE meetings MODIFY status ENUM('scheduled','ongoing','completed') NOT NULL DEFAULT 'scheduled'");
    }
};