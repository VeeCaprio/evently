<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')
                ->unique()
                ->constrained('event_registrations')
                ->cascadeOnDelete();
            $table->dateTime('checked_in_at');
            $table->foreignId('checked_in_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index('checked_in_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
