<?php

namespace TelNowEdge\FreePBX\Base\Resources\Migrations;

interface MigrationInterface
{
    public function setConnection(\FreePBX\Database $connection);

    public function migrate();
}
