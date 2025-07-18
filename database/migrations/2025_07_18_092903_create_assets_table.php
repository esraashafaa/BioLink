<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id(); // ID تلقائي
            $table->string('name'); // اسم الأصل (مثل "صفحة فيسبوك")
            $table->string('link'); // رابط الأصل
            $table->string('icon')->nullable(); // مسار الأيقونة أو اسمها (اختياري)

            $table->enum('type', [
                'facebook',
                'twitter',
                'instagram',
                'linkedin',
                'tiktok',
                'youtube',
                'snapchat',
                'whatsapp',
                'telegram',
                'pinterest',
                'reddit',
                'other'
            ]);

            // مفتاح المستخدم المرتبط
            $table->unsignedBigInteger('userID');
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');

            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
