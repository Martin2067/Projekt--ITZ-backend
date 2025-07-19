<?php
// PHP kód pro konfiguraci databáze
class Database {
    private $host = "localhost"; // Host databáze (obvykle localhost pro XAMPP)
    private $db_name = "it_zakazky"; // Název databáze, kterou jsi vytvořil(a)
    private $username = "root"; // Uživatelské jméno pro MySQL (výchozí pro XAMPP)
    private $password = ""; // Heslo pro MySQL (výchozí pro XAMPP je prázdné)
    public $conn; // Objekt připojení k databázi

    // Metoda pro získání připojení k databázi
    public function getConnection(){
        $this->conn = null;

        try {
            // Vytvoření nového PDO objektu pro připojení
            // PDO (PHP Data Objects) je bezpečnější a modernější způsob připojení k databázi
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                                  $this->username,
                                  $this->password);
            $this->conn->exec("set names utf8mb4"); // Nastavení kódování pro správné zobrazení českých znaků
            // Nastavení režimu chybových zpráv pro PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception){
            echo "Chyba připojení k databázi: " . $exception->getMessage();
            // V reálné aplikaci bys zde logoval(a) chybu, ne ji jen vypisoval(a) uživateli
            exit(); // Ukončí skript, pokud se připojení nezdaří
        }

        return $this->conn; // Vrátí objekt připojení
    }
}
?>