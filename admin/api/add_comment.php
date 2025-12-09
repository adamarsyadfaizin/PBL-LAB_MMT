<?php
// api/add_comment.php - API endpoint untuk menambah komentar menggunakan stored procedure
header('Content-Type: application/json');
include '../config/db.php';

// Enable CORS untuk API calls
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required_fields = ['entity_type', 'entity_id', 'author_name', 'content'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    // Validate entity_type
    $valid_entities = ['news', 'project', 'media'];
    if (!in_array($input['entity_type'], $valid_entities)) {
        throw new Exception('Invalid entity type. Must be: news, project, or media');
    }
    
    // Validate rating if provided
    if (isset($input['rating']) && ($input['rating'] < 1 || $input['rating'] > 5)) {
        throw new Exception('Rating must be between 1 and 5');
    }
    
    // Validate email if provided
    if (!empty($input['author_email']) && !filter_var($input['author_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Prepare parameters for stored procedure
    $params = [
        $input['entity_type'],
        (int)$input['entity_id'],
        $input['author_name'],
        $input['author_email'] ?? null,
        $input['rating'] ?? null,
        $input['content'],
        $input['user_id'] ?? null
    ];
    
    // Call stored procedure
    $result = callStoredProcedure($pdo, 'add_comment', $params);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Komentar berhasil ditambahkan',
            'data' => [
                'entity_type' => $input['entity_type'],
                'entity_id' => $input['entity_id'],
                'author_name' => $input['author_name'],
                'rating' => $input['rating'] ?? null,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception($result['error'] ?? 'Failed to add comment');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
