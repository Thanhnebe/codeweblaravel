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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('old_status')->nullable();  // Trạng thái cũ
            $table->string('new_status');              // Trạng thái mới
            $table->bigInteger('changed_by');           // Người thay đổi
            $table->text('note')->nullable();          // Ghi chú (nếu có)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};
