<?php

require_once('/var/www/html/moodle/config.php');

/**
 * Class BaseModel
 * 
 * 接続情報を本モデルから取得すること
 */
class BaseModel
{
    protected $pdo;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        global $CFG;
        $host = $CFG->dbhost;
        $dbname = $CFG->dbname;
        $username = $CFG->dbuser;
        $password = $CFG->dbpass;

        try {
            $this->pdo = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo '接続に失敗しました。管理者にお問い合わせください。';
            exit();
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
