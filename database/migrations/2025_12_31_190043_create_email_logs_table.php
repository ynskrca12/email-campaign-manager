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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('email_campaigns')->onDelete('set null');
            $table->foreignId('recipient_id')->nullable()->constrained('email_recipients')->onDelete('set null');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->enum('status', ['sent', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index('sent_at');
            $table->index(['campaign_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
