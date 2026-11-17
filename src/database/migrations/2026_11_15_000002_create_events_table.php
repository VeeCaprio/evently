<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_category_id')
                ->nullable()
                ->constrained('event_categories')
                ->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->string('banner')->nullable();
            $table->string('organizer')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('location');
            $table->unsignedInteger('quota')->default(100);
            $table->decimal('ticket_price', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'start_at']);
            $table->index(['status', 'is_featured', 'start_at']);
            $table->index('start_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
