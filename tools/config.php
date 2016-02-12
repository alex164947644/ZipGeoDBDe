<?php

/**
 * Diese Datei enthält die Zugangsdaten zur Verbindung mit der MySql-Datenbank.
 * Sie kann nach Belieben angepasst werden.
 */

$config = [
    'host'          => 'localhost',
    'user'          => 'root',
    'pw'            => 'root',
    'db'            => 'zip_geo_db_de',
];

require_once TOOLS_DIR. DIRECTORY_SEPARATOR. 'MoveData.php';
require_once LIB_DIR. DIRECTORY_SEPARATOR. 'Cli.php';
