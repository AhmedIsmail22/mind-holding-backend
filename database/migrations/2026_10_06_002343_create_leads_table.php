<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['quote', 'demo', 'callback']);
            $table->enum('status', ['new', 'contacted', 'won', 'not_interested', 'spam'])->default('new');
            $table->string('name');
            $table->string('mobile', 20);
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('business_name')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('solution_id')->nullable()->constrained('solutions')->nullOnDelete();
            $table->string('budget')->nullable();
            $table->string('start_timing')->nullable();
            $table->text('project_details')->nullable();
            $table->string('preferred_contact_time')->nullable();
            $table->text('need')->nullable();
            $table->enum('language', ['ar', 'en']);
            $table->string('page_url', 500);
            $table->json('utm')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
