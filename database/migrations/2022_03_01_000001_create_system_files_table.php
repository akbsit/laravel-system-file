<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSystemFilesTable
 */
class CreateSystemFilesTable extends Migration
{
    const TABLE_NAME = 'system_files';

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::create(self::TABLE_NAME, function (Blueprint $oTable) {
            $oTable->id();
            $oTable->string('uniqid')->unique();

            $oTable->boolean('is_partition');

            $oTable->bigInteger('sort')->unsigned()->nullable();

            $oTable->morphs('model');

            $oTable->string('disk_name');
            $oTable->string('collection');
            $oTable->string('dir');
            $oTable->string('mime_type');
            $oTable->string('origin_name');
            $oTable->string('file_name');
            $oTable->bigInteger('file_size')->unsigned();
            $oTable->json('properties')->nullable();

            $oTable->timestamps();

            $oTable->index('uniqid');
            $oTable->index('is_partition');
            $oTable->index('sort');
            $oTable->index('disk_name');
            $oTable->index('collection');
            $oTable->index('dir');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::dropIfExists(self::TABLE_NAME);
    }
}
