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

// Získání ID zakázky z URL parametru
// Příklad URL: http://localhost/it_zakazky_backend/api/jobs/read_one.php?id=1
$id = isset($_GET['id']) ? $_GET['id'] : die(); // Pokud ID chybí, ukončí skript

// Připojení k databázi
$database = new Database();
$db = $database->getConnection();

// Vytvoření instance objektu Job
$job = new Job($db);

// Nastavení ID zakázky, která se má přečíst
$job->id = $id;

// Čtení detailů zakázky
$job->readOne();

// Pokud byla zakázka nalezena (vlastnosti objektu Job jsou nastaveny)
if($job->title != null){
    // Vytvoření pole pro zakázku
    $job_arr = array(
        "id" => $job->id,
        "title" => $job->title,
        "description" => html_entity_decode($job->description), // Dekódování HTML entit
        "client_id" => $job->client_id,
        "status" => $job->status,
        "created_at" => $job->created_at
        // Zde bychom mohli přidat další sloupce jako category_id, budget_from atd.,
        // až je budeme mít plně implementované v modelu a databázi
    );

    // Nastav HTTP status kód - 200 OK
    http_response_code(200);

    // Zobrazení zakázky ve formátu JSON
    echo json_encode($job_arr);
} else {
    // Nastav HTTP status kód - 404 Not found
    http_response_code(404);

    // Informuj uživatele, že zakázka nebyla nalezena
    echo json_encode(
        array("message" => "Zakázka nebyla nalezena.")
    );
}

// Uzavření připojení (pokud máš metodu closeConnection v Database.php, jinak ji odstraň)
// $database->closeConnection();
?>