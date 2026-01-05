<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();

            // librarian yang memproses (nullable karena awalnya member request dulu)
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['requested','approved','borrowed','returned','cancelled','rejected'])
                ->default('requested');

            $table->unsignedInteger('qty')->default(1);

            $table->date('requested_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('borrowed_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
