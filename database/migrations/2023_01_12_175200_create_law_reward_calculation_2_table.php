<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLawRewardCalculation2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('law_reward_calculation_2', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('law_reward_id')->nullable()->comment('ID : law_rewards');
            $table->integer('basic_division_type_id')->nullable()->comment('ID : law_basic_division_type');
            $table->string('division_type_name',255)->nullable()->comment('ชื่อรายการ');
            $table->integer('cal_type')->nullable()->comment('คำนวณเงิน ประเภทการคำนาณ 1.สัดส่วน(%), 2.จำนวนเงิน');
            $table->integer('division')->nullable()->comment('คำนวณเงิน : สัดส่วน(%)');
            $table->decimal('amount',30,2)->nullable()->comment('คำนวณเงิน : จำนวนเงิน');
            $table->integer('average')->nullable()->comment('จำนวนเงินที่ได้รับ : เฉลี่ย');
            $table->decimal('total',30,2)->nullable()->comment('จำนวนเงินที่ได้รับ : จำนวนเงิน');
            $table->text('remark')->nullable()->comment('หมายเหตุ');
            $table->integer('created_by')->nullable()->comment('ผู้บันทึก ID : user_register');
            $table->timestamps();
        });
    } 

    /** 
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('law_reward_calculation_2');
    }
}
