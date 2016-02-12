<?php
/**
 * Created by PhpStorm.
 * User: adachauer
 * Date: 12.02.16
 * Time: 13:12
 */

namespace Webworks\GeoDB\Tools;

/**
 * Diese Klasse dient dazu, die Datensätze vom alten Schema in das neue zu übertragen.
 * Da im alten Schema Duplikate möglich waren, muss es angepasst werden.
 * Siehe Issue #3 auf Github (https://github.com/webworks-nuernberg/ZipGeoDBDe/issues/3).
 *
 * Class MoveData
 * @package Webworks\GeoDB\Tools
 */
class MoveData {

    private $_cli = null;
    /** @var \mysqli */
    private $_connection = null;

    public function __construct(\Cli $cli, $config = []) {
        $this->_cli = $cli;

        $mysqli = new \mysqli($config['host'], $config['user'], $config['pw'], $config['db']);
        if ($mysqli->connect_errno) {
            $cli->print_line(\Cli::color_str("Verbindung fehlgeschlagen: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, \Cli::ColorBgRed));
        } else {
            $cli->print_line(\Cli::color_str("Verbindung erfolgreich!", \Cli::ColorBgBlue));
            $this->_connection = $mysqli;
        }
    }

    public function run() {
        $oldData = $this->loadOldData();
        $this->moveData($oldData);
    }

    private function moveData($oldData) {
        for ($row_no = $oldData->num_rows - 1; $row_no >= 0; $row_no--) {
            $oldData->data_seek($row_no);
            $row = $oldData->fetch_assoc();
            $this->_cli->print_line('Verarbeite Datensatz #'. \Cli::color_str($row['id'], \Cli::ColorFgBrown));
            $sqlInsert = "INSERT INTO `zip_geo_db_de`.`geo_koordinaten`
(`zip`, `location_name`, `lat`, `lon`, `updated_at`)
VALUES ('". $row['zip']. "', '". $row['location_name']. "', '". $row['lat']. "', '". $row['lon']. "', NOW());
";
            try {
                $this->_connection->query($sqlInsert);
            } catch (\Exception $ex) {
                $this->_cli->print_line(\Cli::color_str("Fehler beim Verarbeiten des Datensatzes #". $row['id']. ": (" . $ex->getMessage() . ") ", \Cli::ColorBgRed));
            }
        }
    }

    private function loadOldData() {
        $sql = "SELECT * FROM geo_koordinaten_old;";
        $result = $this->_connection->query($sql);

        if ($result->num_rows > 0) {
            $this->_cli->print_line(\Cli::color_str($result->num_rows. ' ', \Cli::ColorFgBrown). 'Einträge in alter Tabelle gefunden.');
            return $result;
        }

        $this->_cli->print_line(\Cli::color_str("Alte Tabelle ist leer.", \Cli::ColorBgRed));
        return false;
    }
}
