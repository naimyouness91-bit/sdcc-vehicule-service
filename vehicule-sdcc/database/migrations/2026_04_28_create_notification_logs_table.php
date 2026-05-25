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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            
            // User who received the notification
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            
            // Type of notification
            $table->string('notification_type');
            
            // Notification data (JSON)
            $table->json('notification_data')->nullable();
            
            // Delivery channel (database, mail, etc.)
            $table->string('channel')->default('database');
            
            // Send status
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            
            // Error message if failed
            $table->text('error_message')->nullable();
            
            // Retry count
            $table->integer('retry_count')->default(0)->unsigned();
            
            // Role-based filter (what roles were targeted)
            $table->json('targeted_roles')->nullable();
            
            // Related ID (demande_id, user_id, etc.)
            $table->string('related_id')->nullable();
            $table->string('related_type')->nullable();
            
            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            // Indexes
            $table->index('user_id');
            $table->index('notification_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
