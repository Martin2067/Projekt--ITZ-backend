<?php
// PHP kód pro testování databázového připojení a modelů
include_once 'config/Database.php';
include_once 'models/User.php';
include_once 'models/Category.php';

// Získání databázového připojení
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "<h2>Připojení k databázi úspěšné!</h2>";

    // --- Testování modelu User ---
    echo "<h3>Testování modelu User:</h3>";
    $user = new User($db);

    // Test emailExists pro neexistující email
    $user->email = "test@example.com";
    if ($user->emailExists()) {
        echo "<p>E-mail 'test@example.com' <strong>EXISTUJE</strong> (Chyba, neměl by).</p>";
    } else {
        echo "<p>E-mail 'test@example.com' <strong>NEEXISTUJE</strong> (OK, jak má být).</p>";
    }

    // Test vytvoření uživatele
    $user->email = "novy_klient2@example.com";
    $user->password = password_hash("tajneheslo", PASSWORD_BCRYPT); // Hašování hesla pro test
    $user->full_name = "Nový Klient Test2";
    $user->phone = "123456780";
    $user->role = "client";

    if ($user->create()) {
        echo "<p>Uživatel 'Nový Klient Test' byl <strong>úspěšně vytvořen</strong>.</p>";
    } else {
        echo "<p>Chyba při vytváření uživatele 'Nový Klient Test'.</p>";
    }

    // Znovu test emailExists pro nově vytvořený email
    $user->email = "novy_klient@example.com";
    if ($user->emailExists()) {
        echo "<p>E-mail 'novy_klient@example.com' <strong>EXISTUJE</strong> (OK, jak má být).</p>";
        echo "<p>ID nového uživatele: " . $user->id . ", Jméno: " . $user->full_name . ", Role: " . $user->role . "</p>";

        // Test readOne pro nově vytvořeného uživatele
        $user->id = $user->id; // Použijeme ID nově vytvořeného uživatele
        if ($user->readOne()) {
            echo "<p>Načten uživatel ID: " . $user->id . ", Jméno: " . $user->full_name . ", Role: " . $user->role . "</p>";
        } else {
            echo "<p>Chyba při načítání uživatele ID: " . $user->id . "</p>";
        }

        // Test update pro nově vytvořeného uživatele
        $user->full_name = "Aktualizovaný Klient Test";
        $user->phone = "987654321";
        $user->bio = "Jsem aktualizovaný klient.";
        if ($user->update()) {
            echo "<p>Uživatel ID: " . $user->id . " byl <strong>úspěšně aktualizován</strong>.</p>";
            // Ověříme readOne po aktualizaci
            if ($user->readOne()) {
                 echo "<p>Načten aktualizovaný uživatel - Jméno: " . $user->full_name . "</p>";
            }
        } else {
            echo "<p>Chyba při aktualizaci uživatele ID: " . $user->id . "</p>";
        }

    } else {
        echo "<p>E-mail 'novy_klient@example.com' <strong>NEEXISTUJE</strong> (Chyba, měl by).</p>";
    }


    // --- Testování modelu Category ---
    echo "<h3>Testování modelu Category:</h3>";
    $category = new Category($db);
    $stmt = $category->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        echo "<p>Načtené kategorie:</p><ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row); // Extrahuje sloupce jako proměnné ($id, $name, $description)
            echo "<li>ID: {$id}, Název: {$name}, Popis: {$description}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Žádné kategorie nebyly nalezeny.</p>";
    }

} else {
    echo "<h2>Chyba: Nepodařilo se připojit k databázi. Zkontrolujte nastavení v config/Database.php.</h2>";
}
?>