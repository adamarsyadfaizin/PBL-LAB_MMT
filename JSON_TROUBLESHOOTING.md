# Solusi Error Parsing JSON

## Penyebab Error Parsing JSON

Error parsing JSON dalam implementasi stored procedures bisa disebabkan oleh:

### 1. **Invalid JSON Format**
- Karakter ilegal dalam.
- String 
- Escape character yang tidak . 
-
###  2..
-  yang tidak.
- 
- . 

###.
### 叹叹叹叹叹叹叹叹arkan JSON yang Valid

### PHP 
```php
// 
function safeJsonDecode($jsonString) {
    // 
    if (empty($jsonString)) {
        return null;
    }
    
    // 
    $jsonString = trim($jsonString);
    
    // 
    if (substr($jsonString, 0, 1) !== '{' && substr($jsonString, 0, 1) !== '[') {
        return null;
    }
    
    // 
    $data = json_decode($jsonString, true);
    
    // 
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON Error: ' . json_last_error_msg());
    }
    
    return $data;
}

// 
try {
    $input = safeJsonDecode(file_get_contents('php://input'));
    if (!$input) {
        throw new Exception('Invalid or empty JSON input');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
```

### 2. Validasi Input Sebelum Processing

```php
// 
$required_fields = ['entity_type', 'entity_id', 'author_name', 'content'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        throw new Exception("Field '$field' is required");
    }
}

// 
$valid_entities = ['news', 'project', 'media'];
if (!in_array($input['entity_type'], $valid_entities)) {
    throw new Exception('Invalid entity type');
}
```

### 3. Error Handling yang Komprehensif

```php
function handleApiCall($callback) {
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
    
    try {
        $result = $callback();
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => $e->getCode()
        ]);
    }
}
```

### 4. Debugging JSON Issues

```php
// 
function debugJsonInput() {
    $rawInput = file_get_contents('php://input');
    error_log("Raw JSON Input: " . $rawInput);
    error_log("JSON Error: " . json_last_error_msg());
    
    // 
    if (function_exists('json_validate')) { // PHP 8.3+
        if (!json_validate($rawInput)) {
            throw new Exception('Invalid JSON format');
        }
    }
}
```

## Implementasi yang Sudah Diperbaiki

### 1. API Endpoints yang Robust

- `admin/api/add_comment.php` - Form komentar dengan validasi lengkap
- `admin/api/backup_lab_data.php` - Backup data dengan error handling
- `admin/api/cleanup_old_data.php` - Cleanup dengan statistik
- `admin/api/get_lab_stats.php` - Statistik cepat
- `admin/api/refresh_view.php` - Refresh materialized views

### 2. Frontend yang Aman

```javascript
// 
function safeApiCall(url, data, onSuccess, onError) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            onSuccess(result);
        } else {
            onError(result.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('API Error:', error);
        onError('Network error or invalid response');
    });
}
```

### 3. Form Validation di Frontend

```javascript
// 
function validateCommentForm(formData) {
    const errors = [];
    
    if (!formData.author_name || formData.author_name.trim().length < 2) {
        errors.push('Nama minimal 2 karakter');
    }
    
    if (formData.author_email && !isValidEmail(formData.author_email)) {
        errors.push('Format email tidak valid');
    }
    
    if (!formData.content || formData.content.trim().length < 10) {
        errors.push('Komentar minimal 10 karakter');
    }
    
    if (formData.rating && (formData.rating < 1 || formData.rating > 5)) {
        errors.push('Rating harus antara 1-5');
    }
    
    return errors;
}
```

## Best Practices untuk Menghindari JSON Error

### 1. **Selalu Validasi Input**
- Check JSON structure sebelum processing
- Validate required fields
- Sanitize input data

### 2. **Gunakan Try-Catch**
- Wrap semua JSON operations dalam try-catch
- Provide meaningful error messages
- Log errors untuk debugging

### 3. **Test dengan Edge Cases**
- Empty JSON
- Malformed JSON
- Missing fields
- Invalid data types

### 4. **Response Format yang Konsisten**
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation completed",
    "timestamp": "2025-12-08T21:47:00Z"
}
```

## Testing JSON Endpoints

### 1. Manual Testing dengan curl
```bash
# Test valid JSON
curl -X POST http://localhost/admin/api/add_comment.php \
  -H "Content-Type: application/json" \
  -d '{"entity_type":"news","entity_id":1,"author_name":"Test","content":"Test comment"}'

# Test invalid JSON
curl -X POST http://localhost/admin/api/add_comment.php \
  -H "Content-Type: application/json" \
  -d '{"invalid": json}'
```

### 2. Browser Console Testing
```javascript
// Test API call
fetch('admin/api/add_comment.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        entity_type: 'news',
        entity_id: 1,
        author_name: 'Test User',
        content: 'This is a test comment'
    })
})
.then(r => r.json())
.then(console.log);
```

## Troubleshooting Checklist

1. **Check JSON Syntax**
   - Use JSON validator
   - Check for trailing commas
   - Verify quote usage

2. **Check HTTP Headers**
   - Content-Type: application/json
   - Proper CORS headers

3. **Check PHP Settings**
   - `json_last_error()` for specific error
   - Error reporting enabled during development

4. **Check Network**
   - Request size limits
   - Timeout settings
   - SSL/TLS issues

Dengan implementasi ini, error parsing JSON dapat diminimalkan dan lebih mudah di-debug.
