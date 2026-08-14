<?php
declare(strict_types=1);

namespace app\models;

use app\core\database;
use PDO;

abstract class basemodel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = database::getconnection();
    }
}