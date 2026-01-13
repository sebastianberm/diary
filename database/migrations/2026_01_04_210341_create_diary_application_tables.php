<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('moods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('general');
            $table->boolean('is_own_child')->default(false);
            $table->uuid('immich_person_id')->nullable();
            $table->json('keywords')->nullable();
            $table->timestamps();
        });

        Schema::create('diary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->longText('content')->nullable();
            $table->foreignId('mood_id')->nullable()->constrained('moods')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });

        Schema::create('entry_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('diary_entries')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('source')->default('manual');
            $table->timestamps();
        });

        Schema::create('children_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('entry_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('diary_entries')->cascadeOnDelete();
            $table->string('immich_asset_id');
            $table->string('local_path')->nullable();
            $table->text('caption')->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_photos');
        Schema::dropIfExists('children_logs');
        Schema::dropIfExists('entry_interactions');
        Schema::dropIfExists('diary_entries');
        Schema::dropIfExists('people');
        Schema::dropIfExists('moods');
    }
};
