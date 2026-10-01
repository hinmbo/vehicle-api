<?php

class VehicleValidator {
  public static function validate(array $data, bool $isUpdate = false): array {
    $errors = [];

    // model_name required
    if (!$isUpdate && isset($data['model_name'])) {
      if (empty($data['model_name']) || !is_string($data['model_name'])) {
        $errors['model_name'] = 'model_name is required and must be a string.';
      }
    }

    // type_id required & integer
    if (!$isUpdate && isset($data['type_id'])) {
      if (empty($data['type_id']) || !filter_var($data['type_id'], FILTER_VALIDATE_INT)) {
        $errors[] = "type_id is required and must be an integer.";
      }
    }

    // vehicle_type
    if (empty($data['vehicle_type']) || !is_string($data['vehicle_type'])) {
      $errors[] = "vehicle_type is required and must be a string.";
    }

    // doors number > 0
    if (!$isUpdate || isset($data['doors'])) {
      if (!isset($data['doors']) || !filter_var($data['doors'], FILTER_VALIDATE_INT) || $data['doors'] <= 0) {
        $errors[] = "doors must be an integer greater than 0.";
      }
    }

    // transmission: manual / automatic
    if (!$isUpdate || isset($data['transmission'])) {
      $allowedTransmissions = ['manual', 'automatic'];
      if (empty($data['transmission']) || !in_array($data['transmission'], $allowedTransmissions)) {
        $errors[] = "transmission must be either 'manual' or 'automatic'.";
      }
    }

    // fuel: petrol / diesel / hybrid / electric
    if (!$isUpdate || isset($data['fuel'])) {
      $allowedFuel = ['petrol', 'diesel', 'hybrid', 'electric'];
      if (empty($data['fuel']) || !in_array($data['fuel'], $allowedFuel, true)) {
        $errors[] = "fuel must be one of: petrol, diesel, hybrid, electric.";
      }
    }

    // price number >= 0
    if (!$isUpdate || isset($data['price'])) {
      if (!isset($data['price']) || !is_numeric($data['price']) || (float)$data['price'] < 0) {
        $errors[] = "price must be a number greater than or equal to 0.";
      }
    }

    return $errors;
  }
}