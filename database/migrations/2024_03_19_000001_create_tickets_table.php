<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ticket;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', [
                Ticket::PRIORITY_CRITICAL,
                Ticket::PRIORITY_HIGH,
                Ticket::PRIORITY_MEDIUM,
                Ticket::PRIORITY_LOW
            ])->default(Ticket::PRIORITY_MEDIUM);
            $table->enum('status', [
                Ticket::STATUS_OPEN,
                Ticket::STATUS_CLOSED
            ])->default(Ticket::STATUS_OPEN);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
