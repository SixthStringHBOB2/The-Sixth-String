<?php
session_start();

// Get JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Save the filters in the session
if ($data) {
    $_SESSION['selectedFilters'] = [
        'brands' => isset($data['brands']) ? $data['brands'] : [],
        'categories' => isset($data['categories']) ? $data['categories'] : [],
        'price' => [
            'min' => isset($data['price']['min']) ? $data['price']['min'] : 0,
            'max' => isset($data['price']['max']) ? $data['price']['max'] : 10000,
        ],
    ];

    echo json_encode(['status' => 'success', 'message' => 'Filters saved']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
