<?php
require 'vendor/autoload.php';


$mongoDBUri = "mongodb://localhost:27017"; 
$client = new MongoDB\Client($mongoDBUri);
$database = $client->selectDatabase('registerations');
$collection = $database->selectCollection('vehiclespassed');


$data = json_decode(file_get_contents('php://input'), true);

$existingVehicle = $collection->findOne(['registration_number' => $data['registration_number']]);

if ($existingVehicle) {

    echo json_encode(['success' => false, 'message' => 'Duplicate entry. Vehicle already exists.']);
} else {

    $result = $collection->insertOne([
        'registration_number' => $data['registration_number'],
        'owner_name' => $data['owner_name'],
        'vehicle_type' => $data['vehicle_type'],
        'totalfine' => $data['total_fine'],
        'phone_number' => $data['phone_number'],
        'time_passed' => $data['time_passed'],
        'status' => 'Message sent' 
    ]);


    echo json_encode(['success' => true, 'inserted_id' => (string)$result->getInsertedId()]);
}
?>
