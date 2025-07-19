<?php
// Požadované hlavičky pro API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Zahrnutí databázového připojení a modelu User
include_once '../../config/Database.php';
include_once '../../models/User.php';

// Získání databázového připojení
$database = new Database();
$db = $database->getConnection();

// Vytvoření objektu User
$user = new User($db);

// Získání dat z POST požadavku
$data = json_decode(file_get_contents("php://input"));

// Ověření, zda data nejsou prázdná
if (!empty($data->email) && !empty($data->password)) {
    // Nastavení emailu pro ověření existence
    $user->email = $data->email;

    // Zkontrolovat, zda e-mail existuje a heslo souhlasí
    if ($user->emailExists() && password_verify($data->password, $user->password)) {
        // Přihlášení úspěšné
        http_response_code(200); // OK

        // Vytvoření pole dat o uživateli, které pošleme zpět frontendu
        // (Nikdy neposílej hašované heslo zpět na frontend!)
        $user_arr = array(
            "message" => "Úspěšné přihlášení.",
            "user_id" => $user->id,
            "full_name" => $user->full_name,
            "email" => $user->email,
            "role" => $user->role
            // Zde by se v reálné aplikaci vygeneroval JWT token
            // Pro teď stačí tyto základní informace
        );
        echo json_encode($user_arr);

    } else {
        // Přihlášení neúspěšné (špatný e-mail nebo heslo)
        http_response_code(401); // Unauthorized
        echo json_encode(array("message" => "Přihlášení selhalo. Neplatné přihlašovací údaje."));
    }
} else {
    // Chybí data (e-mail nebo heslo)
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Nebylo možné se přihlásit. Chybí data."));
}
?>