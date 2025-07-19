<?php
class Job {
    // Připojení k databázi a název tabulky
    private $conn;
    private $table_name = "jobs";

    // Vlastnosti objektu
    public $id;
    public $title;
    public $description;
    public $client_id;
    public $status;
    public $created_at;

    // Konstruktor s $db připojením
    public function __construct($db){
        $this->conn = $db;
    }

    // Metoda pro čtení všech zakázek (již máš)
    function readAll(){
        $query = "SELECT
                    id, title, description, client_id, status, created_at
                FROM
                    " . $this->table_name . "
                ORDER BY
                    created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Metoda pro čtení jedné zakázky
    function readOne(){
        // Dotaz pro čtení jedné zakázky
        $query = "SELECT
                    id, title, description, client_id, status, created_at
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1"; // Limit 0,1 zajistí, že se vrátí maximálně jeden záznam

        // Příprava dotazu
        $stmt = $this->conn->prepare( $query );

        // Navázání ID (otazník v dotazu)
        $stmt->bindParam(1, $this->id);

        // Spuštění dotazu
        $stmt->execute();

        // Získání načteného řádku
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nastavení hodnot vlastností objektu
        if ($row) { // Zkontroluj, zda byl řádek nalezen
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->client_id = $row['client_id'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
        } else {
            // Pokud zakázka nebyla nalezena, nastav vlastnosti na null nebo výchozí hodnoty
            $this->title = null;
            $this->description = null;
            $this->client_id = null;
            $this->status = null;
            $this->created_at = null;
        }
    }

    // Metoda pro vytvoření zakázky (již máš)
    function create(){
        // ... tvůj existující kód pro create ...
    }
}
?>