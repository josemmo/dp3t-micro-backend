<?php
require __DIR__ . "/bootstrap.php";

use App\Utils\DB;

// Get system information
echo "[i] Fetching system information...\n";
$engines = array_column(DB::getAll('SHOW ENGINES'), 'Engine');
$supportsAria = in_array('Aria', $engines);

// 2020-04-20 :: rev 1
echo "[i] Creating 'exposees' table...\n";
DB::query(
    'CREATE TABLE IF NOT EXISTS exposees (
        `key` binary(32) NOT NULL,
        `key_date` date NOT NULL,
        `uploaded_at` datetime NOT NULL,
        PRIMARY KEY (`key`),
        KEY `key_date` (`key_date`),
        KEY `upload_date` (`uploaded_at`)
     ) ENGINE=?p DEFAULT CHARSET=utf8',
    $supportsAria ? 'Aria' : 'InnoDB'
);

// Exit script
echo "[i] Finished installing!\n";
