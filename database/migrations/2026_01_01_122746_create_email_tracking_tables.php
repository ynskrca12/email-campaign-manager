<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Email Opens Tracking
        Schema::create('email_opens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('email_campaigns')->onDelete('set null');
            $table->foreignId('recipient_id')->nullable()->constrained('email_recipients')->onDelete('set null');
            $table->string('email');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->timestamp('opened_at');
            $table->timestamps();

            $table->index(['campaign_id', 'email']);
            $table->index('opened_at');
        });

        // Link Clicks Tracking
        Schema::create('email_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('email_campaigns')->onDelete('set null');
            $table->foreignId('recipient_id')->nullable()->constrained('email_recipients')->onDelete('set null');
            $table->string('email');
            $table->text('original_url');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index(['campaign_id', 'email']);
            $table->index('clicked_at');
        });

        // Tracking Links (URL mapping)
        Schema::create('tracking_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('email_campaigns')->onDelete('cascade');
            $table->text('original_url');
            $table->string('tracking_hash', 32)->unique();
            $table->integer('click_count')->default(0);
            $table->timestamps();

            $table->index('tracking_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_clicks');
        Schema::dropIfExists('email_opens');
        Schema::dropIfExists('tracking_links');
    }
};
