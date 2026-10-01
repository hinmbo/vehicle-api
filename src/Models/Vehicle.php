<?php

class Vehicle {
  private PDO $conn;
  private string $table = "vehicles";

  public function __construct(PDO $db) {
    $this->conn = $db;
  }

  // Get Vehicles με φίλτρα & ταξινόμηση
  public function getAll(array $filters): array {
    $query = "SELECT id, model_name, type_id, vehicle_type, doors, transmission, fuel, price FROM " . $this->table . " WHERE 1=1";
    $params = [];

    // 1. Price Filters
    if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
      $query .= " AND price >= :price_min";
      $params[':price_min'] = (float)$filters['price_min'];
    }
    if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
      $query .= " AND price <= :price_max";
      $params[':price_max'] = (float)$filters['price_max'];
    }

    // 2. Transmission Filter
    if (!empty($filters['transmission']) && in_array($filters['transmission'], ['manual', 'automatic'], true)) {
      $query .= " AND transmission = :transmission";
      $params[':transmission'] = $filters['transmission'];
    }

    // 3. Type Filter
    if (isset($filters['type_id']) && filter_var($filters['type_id'], FILTER_VALIDATE_INT ) !== false) {
      $query .= " AND type_id = :type_id";
      $params[':type_id'] = (int)$filters['type_id'];
    }

    // 4. Sorting Filter
    $sortQuery = " ORDER BY id ASC"; // Default
    if (!empty($filters['sort'])) {
      $sortMap = [
        'name_asc' => 'model_name ASC',
        'name_desc' => 'model_name DESC',
        'price_asc' => 'price ASC',
        'price_desc' => 'price DESC',
      ];
      if (isset($sortMap[$filters['sort']])) {
        $sortQuery = " ORDER BY " . $sortMap[$filters['sort']];
      }
    }
    $query .= $sortQuery;

    $stmt = $this->conn->prepare($query);
    foreach ($params as $key => $val) {
      $stmt->bindValue($key, $val);
    }
    $stmt->execute();

    $results = $stmt->fetchAll();

    // Cast αριθημτικών τιμών σε σωστά types για το JSON output
    return array_map(function($row) {
      return [
        'id'            => (int)$row['id'],
        'model_name'    => $row['model_name'],
        'type_id'       => (int)$row['type_id'],
        'vehicle_type'  => $row['vehicle_type'],
        'doors'         => (int)$row['doors'],
        'transmission'  => $row['transmission'],
        'fuel'          => $row['fuel'],
        'price'         => (float)$row['price']
      ];
    }, $results);
  }

  // Find by ID
  public function getById(int $id): ?array {
    $stmt = $this->conn->prepare("SELECT id, model_name, type_id, vehicle_type, doors, transmission, fuel, price FROM " . $this->table . " WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) return null;

    return [
      'id'            => (int)$row['id'],
      'model_name'    => $row['model_name'],
      'type_id'       => (int)$row['type_id'],
      'vehicle_type'  => $row['vehicle_type'],
      'doors'         => (int)$row['doors'],
      'transmission'  => $row['transmission'],
      'fuel'          => $row['fuel'],
      'price'         => (float)$row['price']
    ];
  }

  // Create Vehicle
  public function create(array $data): int {
    $query = "INSERT INTO " . $this->table . " (model_name, type_id, vehicle_type, doors, transmission, fuel, price)
              VALUES (:model_name, :type_id, :vehicle_type, :doors, :transmission, :fuel, :price)";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      ':model_name'   => $data['model_name'],
      ':type_id'      => (int)$data['type_id'],
      ':vehicle_type' => $data['vehicle_type'],
      ':doors'        => (int)$data['doors'],
      ':transmission' => $data['transmission'],
      ':fuel'         => $data['fuel'],
      ':price'        => (float)$data['price']
    ]);

    return (int)$this->conn->lastInsertId();
  }

  // Update Vehicle
  public function update(int $id, array $data): bool {
    $existing = $this->getById($id);
    if (!$existing) return false;

    $merged = array_merge($existing, $data);

    $query = "UPDATE " . $this->table . "
              SET model_name = :model_name, type_id = :type_id, vehicle_type = :vehicle_type,
                  doors = :doors, transmission = :transmission, fuel = :fuel, price = :price
                  WHERE id = :id";

    $stmt = $this->conn->prepare($query);
    return $stmt->execute([
      ':model_name'   => $merged['model_name'],
      ':type_id'      => (int)$merged['type_id'],
      ':vehicle_type' => $merged['vehicle_type'],
      ':doors'        => (int)$merged['doors'],
      ':transmission' => $merged['transmission'],
      ':fuel'         => $merged['fuel'],
      ':price'        => (float)$merged['price'],
      ':id'           => $id
    ]);
  }

  // Delete Vehicle
  public function delete(int $id): bool {
    $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
  }
}