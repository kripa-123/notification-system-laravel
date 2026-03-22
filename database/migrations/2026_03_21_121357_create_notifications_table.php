<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('notifications', function (Blueprint $table) {
    //         $table->id();
    //         $table->timestamps();
    //     });
    // }

    public function up(): void
{
    Schema::create('notifications', function (Blueprint $table) {
        $table->uuid('id')->primary(); // UUID for distributed scaling
        $table->unsignedBigInteger('tenant_id')->index(); // Multi-tenant support 
        $table->unsignedBigInteger('user_id')->index();   // For rate limiting [cite: 23]
        
        $table->string('type'); // email, sms, push [cite: 28]
        $table->json('payload'); // Flexible storage for message content
        
        // Status Management [cite: 13, 17]
        $table->enum('status', ['pending', 'processing', 'sent', 'failed'])->default('pending');
        $table->integer('retry_count')->default(0);
        $table->text('error_message')->nullable();

        $table->timestamps();
        
        // Composite index for the Summary API performance [cite: 17, 30]
        $table->index(['tenant_id', 'status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
