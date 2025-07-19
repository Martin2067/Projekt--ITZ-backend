<?php
// Požadované hlavičky pro API
header("Access-Control-Allow-Origin: *"); // Povolí přístup z libovolného zdroje (pro vývoj)
header("Content-Type: application/json; charset=UTF-8"); // Odpověď bude ve formátu JSON
header("Access-Control-Allow-Methods: POST"); // Povolená metoda je POST
header("Access-Control-Max-Age: 3600"); // Jak dlouho může být výsledek preflight požadavku kešován
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Povolené hlavičky

// Zahrnutí databázového připojení a modelu User
include_once '../../config/Database.php';
include_once '../../models/User.php';

// Získání databázového připojení
$database = new Database();
$db = $database->getConnection();

// Vytvoření objektu User
$user = new User($db);

// Získání dat z POST požadavku
// Funkce json_decode převede JSON řetězec na PHP objekt
$data = json_decode(file_get_contents("php://input"));

// Ověření, zda data nejsou prázdná a obsahují potřebné údaje
if (
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->full_name) &&
    !empty($data->role) // Klíčové pro rozlišení klienta/experta
) {
    // Nastavení hodnot vlastností objektu User
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_BCRYPT); // Heslo hašujeme pro bezpečnost!
    $user->full_name = $data->full_name;
    $user->phone = isset($data->phone) ? $data->phone : null; // Telefon je volitelný
    $user->role = $data->role; // 'client' nebo 'expert'

    // Zkontrolovat, zda e-mail již neexistuje
    if ($user->emailExists()) {
        // Pokud e-mail existuje, vrátíme chybu 409 Conflict
        http_response_code(409); // Conflict
        echo json_encode(array("message" => "E-mail je již používán."));
    } else {
        // Pokus o vytvoření uživatele
        if ($user->create()) {
            // Úspěšná registrace, vrátíme stav 201 Created
            http_response_code(201); // Created
            echo json_encode(array("message" => "Uživatel byl úspěšně zaregistrován.", "user_id" => $user->id));
        } else {
            // Chyba při vytváření uživatele, vrátíme stav 503 Service Unavailable
            http_response_code(503); // Service Unavailable
            echo json_encode(array("message" => "Nebylo možné zaregistrovat uživatele."));
        }
    }
} else {
    // Chybí potřebná data, vrátíme stav 400 Bad Request
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Nebylo možné zaregistrovat uživatele. Chybí data."));
}
?>