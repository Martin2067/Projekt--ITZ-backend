<?php
// PHP CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Povolíme POST a OPTIONS
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json"); // Odpověď bude JSON

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';
require_once '../../models/Job.php'; // Zde se načte třída Job

// Zkontroluj, zda je metoda POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Metoda není povolena."]);
    exit();
}

// Získání dat z těla požadavku
$data = json_decode(file_get_contents("php://input"));

// Základní validace vstupních dat
if (empty($data->title) || empty($data->description) || empty($data->client_id)) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Nevyplněná povinná pole (title, description, client_id)."]);
    exit();
}

// Zkontroluj, zda je client_id platné číslo
if (!is_numeric($data->client_id)) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Neplatné client_id."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$job = new Job($db); // <--- ZDE JE OPRAVA! Používáme třídu Job, která je načtena z modelu

// Nastav hodnoty pro objekt zakázky
$job->title = $data->title;
$job->description = $data->description;
$job->client_id = $data->client_id;
// 'status' a 'created_at' se nastaví automaticky v databázi nebo modelu

// Vytvoř zakázku
if ($job->create()) {
    http_response_code(201); // Created
    echo json_encode(["message" => "Zakázka byla úspěšně vytvořena.", "job_id" => $job->id]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Nepodařilo se vytvořit zakázku."]);
}

// Tento řádek bychom měli odstranit, pokud v Database.php nemáme closeConnection() metodu
// Jak jsem psal dříve, PHP si připojení uzavře samo po skončení skriptu.
// Pokud nemáš metodu closeConnection v config/database.php, tak tento řádek odstraň nebo zakomentuj.
// $database->closeConnection(); 

?>