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
        Schema::create('email_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('email_campaigns')->onDelete('cascade');
            $table->string('email');
            $table->string('name')->nullable();
            $table->json('custom_fields')->nullable(); // Kişiselleştirme için
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_recipients');
    }
};
