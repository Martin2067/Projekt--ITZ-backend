<?php
// PHP kód pro model Category
class Category {
    // Připojení k databázi a název tabulky
    private $conn;
    private $table_name = "categories";

    // Vlastnosti objektu Category
    public $id;
    public $name;
    public $description;

    // Konstruktor s připojením k databázi
    public function __construct($db){
        $this->conn = $db;
    }

    // Metoda pro načtení všech kategorií
    public function read(){
        // SQL dotaz pro výběr všech kategorií
        $query = "SELECT id, name, description
                  FROM " . $this->table_name . "
                  ORDER BY name";

        // Připravení dotazu
        $stmt = $this->conn->prepare($query);

        // Spuštění dotazu
        $stmt->execute();

        return $stmt; // Vrátí PDOStatement objekt, ze kterého můžeme načítat řádky
    }
}
?>