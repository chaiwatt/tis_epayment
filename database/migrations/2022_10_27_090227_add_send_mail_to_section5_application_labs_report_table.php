<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendMailToSection5ApplicationLabsReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section5_application_labs_report', function (Blueprint $table) {
            $table->integer('send_mail_status')->nullable()->comment('สถานะแจ้งเตือนอีเมล');
            $table->text('noti_email')->nullable()->comment('อีเมลที่แจ้งผล');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section5_application_labs_report', function (Blueprint $table) {
            $table->dropColumn(['send_mail_status','noti_email']);
        });
    }
}
