<?php
namespace Grim\Utils;

use PDO;

class ResultLogger
{
    private $db;

    public function __construct($dbFile = __DIR__ . '/../../results/results.db')
    {
        if (!is_dir(dirname($dbFile))) {
            mkdir(dirname($dbFile), 0755, true);
        }
        $this->db = new PDO('sqlite:' . $dbFile);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->init();
    }

    private function init()
    {
        $this->db->exec('CREATE TABLE IF NOT EXISTS scan_results (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            target TEXT,
            scan_time TEXT,
            result_json TEXT
        )');
    }

    public function logResult($target, $result)
    {
        $stmt = $this->db->prepare('INSERT INTO scan_results (target, scan_time, result_json) VALUES (?, ?, ?)');
        $stmt->execute([$target, date('Y-m-d H:i:s'), json_encode($result)]);
    }

    public function listResults()
    {
        return $this->db->query('SELECT id, target, scan_time FROM scan_results ORDER BY scan_time DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResult($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM scan_results WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
