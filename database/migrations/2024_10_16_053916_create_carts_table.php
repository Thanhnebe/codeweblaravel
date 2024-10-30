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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();  // Tự động tạo cột 'id' kiểu bigint UNSIGNED AUTO_INCREMENT
            $table->bigInteger('user_id'); // Liên kết với bảng users
            $table->bigInteger('product_id'); // Liên kết với bảng products
            $table->bigInteger('variant_id')->nullable(); // Liên kết với bảng variants, có thể NULL
            $table->integer('quantity'); // Số lượng sản phẩm
            $table->decimal('price', 30, 0); // Giá của sản phẩm hoặc biến thể
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
