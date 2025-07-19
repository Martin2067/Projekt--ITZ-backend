<?php
// PHP kód pro model User
class User {
    // Připojení k databázi a název tabulky
    private $conn;
    private $table_name = "users";

    // Vlastnosti objektu User
    public $id;
    public $email;
    public $password;
    public $full_name;
    public $phone;
    public $role; // client nebo expert
    public $bio;
    public $portfolio_url;
    public $hourly_rate;
    public $price_type;
    public $facebook_id;
    public $google_id;
    public $created_at;
    public $updated_at;

    // Konstruktor s připojením k databázi
    public function __construct($db){
        $this->conn = $db;
    }

    // Metoda pro vytvoření (registraci) nového uživatele
    public function create() {
        // SQL dotaz pro vložení záznamu
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                      email = :email,
                      password = :password,
                      full_name = :full_name,
                      phone = :phone,
                      role = :role";

        // Připravení dotazu
        $stmt = $this->conn->prepare($query);

        // Očištění dat (odstranění HTML značek a bílých znaků)
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->role = htmlspecialchars(strip_tags($this->role));

        // Vazba hodnot
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password); // Heslo bude hašované
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role", $this->role);

        // Spuštění dotazu
        if ($stmt->execute()) {
            return true;
        }

        // Pokud dojde k chybě, vypíšeme ji (pro ladění)
        printf("Chyba: %s.\n", $stmt->error);
        return false;
    }

    // Metoda pro zjištění, zda e-mail existuje (pro registraci a přihlášení)
    public function emailExists(){
        // SQL dotaz pro ověření existence e-mailu
        $query = "SELECT id, full_name, password, role
                  FROM " . $this->table_name . "
                  WHERE email = ?
                  LIMIT 0,1";

        // Připravení dotazu
        $stmt = $this->conn->prepare( $query );

        // Vazba e-mailu
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);

        // Spuštění dotazu
        $stmt->execute();

        // Získání počtu řádků
        $num = $stmt->rowCount();

        // Pokud e-mail existuje, přiřadíme vlastnosti objektu
        if ($num > 0) {
            // Získání hodnot řádku
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Přiřazení hodnot vlastnostem objektu
            $this->id = $row['id'];
            $this->full_name = $row['full_name'];
            $this->password = $row['password']; // Hašované heslo z DB
            $this->role = $row['role'];

            return true;
        }

        return false;
    }

    // Metoda pro získání informací o uživateli podle ID (např. pro zobrazení profilu)
    public function readOne(){
        $query = "SELECT id, email, full_name, phone, role, bio, portfolio_url, hourly_rate, price_type, created_at
                  FROM " . $this->table_name . "
                  WHERE id = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->email = $row['email'];
            $this->full_name = $row['full_name'];
            $this->phone = $row['phone'];
            $this->role = $row['role'];
            $this->bio = $row['bio'];
            $this->portfolio_url = $row['portfolio_url'];
            $this->hourly_rate = $row['hourly_rate'];
            $this->price_type = $row['price_type'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Metoda pro aktualizaci profilu uživatele
    public function update(){
        $query = "UPDATE " . $this->table_name . "
                  SET
                      full_name = :full_name,
                      phone = :phone,
                      bio = :bio,
                      portfolio_url = :portfolio_url,
                      hourly_rate = :hourly_rate,
                      price_type = :price_type
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Očištění a vazba dat
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->portfolio_url = htmlspecialchars(strip_tags($this->portfolio_url));
        $this->hourly_rate = htmlspecialchars(strip_tags($this->hourly_rate));
        $this->price_type = htmlspecialchars(strip_tags($this->price_type));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':portfolio_url', $this->portfolio_url);
        $stmt->bindParam(':hourly_rate', $this->hourly_rate);
        $stmt->bindParam(':price_type', $this->price_type);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Chyba: %s.\n", $stmt->error);
        return false;
    }
}
?>