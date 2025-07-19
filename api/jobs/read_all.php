<?php
// PHP CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Povolíme GET a OPTIONS
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json"); // Odpověď bude JSON

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';
require_once '../../models/Job.php';

// Zkontroluj, zda je metoda GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Metoda není povolena."]);
    exit();
}

// Připojení k databázi
$database = new Database();
$db = $database->getConnection();

// Vytvoření instance objektu Job
$job = new Job($db);

// Získání zakázek
$stmt = $job->readAll();
$num = $stmt->rowCount();

// Zkontroluj, zda byly nalezeny nějaké zakázky
if($num > 0){
    // Pole zakázek
    $jobs_arr = array();
    $jobs_arr["records"] = array();

    // Načtení obsahu tabulky
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // Extrahování řádku pro snazší přístup (např. $row['title'] místo $job->title)
        extract($row);

        $job_item = array(
            "id" => $id,
            "title" => $title,
            "description" => html_entity_decode($description), // Dekódování HTML entit
            "client_id" => $client_id,
            "status" => $status,
            "created_at" => $created_at
            // Zde bychom mohli přidat další sloupce jako category_id, budget_from atd.,
            // až je budeme mít plně implementované v modelu a databázi
        );

        array_push($jobs_arr["records"], $job_item);
    }

    // Nastav HTTP status kód - 200 OK
    http_response_code(200);

    // Zobrazení zakázek ve formátu JSON
    echo json_encode($jobs_arr);
} else {
    // Nastav HTTP status kód - 404 Not found
    http_response_code(404);

    // Informuj uživatele, že nebyly nalezeny žádné zakázky
    echo json_encode(
        array("message" => "Žádné zakázky nebyly nalezeny.")
    );
}

// Uzavření připojení (pokud máš metodu closeConnection v Database.php, jinak ji odstraň)
// $database->closeConnection();
?>