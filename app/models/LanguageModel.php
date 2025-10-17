<?php 
namespace App\Models;
use App\Core\Lib\DB;
use PDO;

class LanguageModel {
    protected $table = 'languages';
    protected $db;

    public function __construct() {
        $this->db = DB::getInstance()->db(); // PDO instance
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
