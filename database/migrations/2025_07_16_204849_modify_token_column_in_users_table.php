<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // إزالة المفتاح الفريد أولاً
            $table->dropUnique('users_token_unique');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // تغيير نوع العمود إلى text
            $table->text('token')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // تغيير العمود إلى string أولاً بدون مفتاح فريد
            $table->string('token', 80)->nullable()->change();
        });
        
        Schema::table('users', function (Blueprint $table) {
            // إضافة المفتاح الفريد بعد تغيير نوع العمود
            $table->unique('token', 'users_token_unique');
        });
    }
};
