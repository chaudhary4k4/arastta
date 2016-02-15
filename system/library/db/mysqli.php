<?php
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015 Arastta Association. All rights reserved. (arastta.org)
 * @credits        See CREDITS.txt for credits and other copyright notices.
 * @license        GNU General Public License version 3; see LICENSE.txt
 */

namespace DB;

final class MySQLi {

    private $link;

    public function __construct($hostname, $username, $password, $database) {
        $this->link = new \mysqli($hostname, $username, $password, $database);

        if ($this->link->connect_error) {
            trigger_error('Error: Could not make a database link (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
            exit();
        }

        $this->link->set_charset("utf8");
        $this->link->query("SET SQL_MODE = ''");
    }

    public function query($sql) {
        $query = $this->link->query($sql);

        if (!$this->link->errno) {
            if ($query instanceof \mysqli_result) {
                $data = array();

                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;

                $query->close();

                return $result;
            } else {
                return true;
            }
        } else {
            trigger_error('Error: ' . $this->link->error  . '<br />Error No: ' . $this->link->errno . '<br />' . $sql);
        }
    }

    public function escape($value) {
        return $this->link->real_escape_string($value);
    }

    public function countAffected() {
        return $this->link->affected_rows;
    }

    public function getLastId() {
        return $this->link->insert_id;
    }

    public function getVersion() {
        return mysqli_get_server_info($this->link);
    }

    public function getCollation() {
        // Get the database collation from server
        $result = $this->query('SHOW VARIABLES LIKE "collation_database"');

        if (!empty($result->row) && isset($result->row['Value'])) {
            return $result->row['Value'];
        } else {
            return '';
        }
    }

    public function __destruct() {
        $this->link->close();
    }
}
