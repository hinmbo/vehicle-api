<?php

require_once __DIR__ . '/../Models/Vehicle.php';
require_once __DIR__ . '/../Validation/VehicleValidator.php';

class VehicleController {
  private Vehicle $vehicleModel;

  public function __construct(PDO $db) {
    $this->vehicleModel = new Vehicle($db);
  }

  // GET /vehicles
  public function index(array $queryParams): void {
    $vehicles = $this->vehicleModel->getAll($queryParams);
    $this->jsonResponse(200, $vehicles);
  }

  // POST /vehicles
  public function store(): void {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $errors = VehicleValidator::validate($input);
    if (!empty($errors)) {
      $this->jsonResponse(400, ['errors' => $errors]);
      return;
    }

    $id = $this->vehicleModel->create($input);
    $newVehicle = $this->vehicleModel->getById($id);

    $this->jsonResponse(201, $newVehicle);
  }

  // PUT /vehicles/{id}
  public function update(int $id): void {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $existing = $this->vehicleModel->getById($id);
    if (!$existing) {
      $this->jsonResponse(404, ["message" => "Vehicle not found."]);
      return;
    }

    $errors = VehicleValidator::validate($input, true);
    if (!empty($errors)) {
      $this->jsonResponse(400, ['errors' => $errors]);
      return;
    }

    $this->vehicleModel->update($id, $input);
    $updatedVehicle = $this->vehicleModel->getById(($id));

    $this->jsonResponse(200, $updatedVehicle);

  }

  // DELETE /vehicles/{id}
  public function destroy(int $id): void {
    $success = $this->vehicleModel->delete($id);
    if (!$success) {
      $this->jsonResponse(404, ["message" => "Vehicle not found."]);
      return;
    }

    $this->jsonResponse(200, ["message" => "Vehicle deleted successfully."]);

  }

  private function jsonResponse(int $statusCode, mixed $data): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }
}