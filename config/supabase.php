<?php
// supabase.php
$supabase_url = 'https://tkjnbelcgfwpbhppsnrl.supabase.co';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRram5iZWxjZ2Z3cGJocHBzbnJsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjE3NDA3NjIsImV4cCI6MjA3NzMxNjc2Mn0.wOjK4X2qJV6LzOG4yXxnfeTezDX5_3Sb3wezhCuQAko';

// Fungsi helper untuk query Supabase menggunakan curl
function supabaseQuery($table, $params = [])
{
    global $supabase_url, $supabase_key;
    
    $url = $supabase_url . '/rest/v1/' . $table;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $supabase_key,
            'Authorization: Bearer ' . $supabase_key,
            'Content-Type: application/json',
        ],
    ]);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    return [
        'success' => $statusCode >= 200 && $statusCode < 300,
        'data' => $data,
        'status' => $statusCode
    ];
}

// Fungsi helper untuk insert data ke Supabase
function supabaseInsert($table, $data)
{
    global $supabase_url, $supabase_key;
    
    $url = $supabase_url . '/rest/v1/' . $table;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $supabase_key,
            'Authorization: Bearer ' . $supabase_key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ],
    ]);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return [
        'success' => $statusCode >= 200 && $statusCode < 300,
        'data' => $result,
        'status' => $statusCode
    ];
}

// Fungsi helper untuk update data di Supabase
function supabaseUpdate($table, $data, $column, $value)
{
    global $supabase_url, $supabase_key;
    
    $url = $supabase_url . '/rest/v1/' . $table . '?' . $column . '=eq.' . $value;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $supabase_key,
            'Authorization: Bearer ' . $supabase_key,
            'Content-Type: ' . 'application/json',
            'Prefer: return=representation'
        ],
    ]);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return [
        'success' => $statusCode >= 200 && $statusCode < 300,
        'data' => $result,
        'status' => $statusCode
    ];
}

// Fungsi untuk mengecek apakah username sudah ada
function checkUsernameExists($username)
{
    $result = supabaseQuery('users', [
        'select' => 'id',
        'username' => 'eq.' . $username
    ]);
    
    return $result['success'] && count($result['data']) > 0;
}

// Fungsi untuk mengecek apakah email sudah ada
function checkEmailExists($email)
{
    $result = supabaseQuery('users', [
        'select' => 'id',
        'email' => 'eq.' . $email
    ]);
    
    return $result['success'] && count($result['data']) > 0;
}

// Fungsi untuk mendapatkan user by username
function getUserByUsername($username)
{
    $result = supabaseQuery('users', [
        'select' => '*',
        'username' => 'eq.' . $username
    ]);
    
    if ($result['success'] && count($result['data']) > 0) {
        return $result['data'][0];
    }
    
    return null;
}

// Fungsi untuk mendapatkan user by email
function getUserByEmail($email)
{
    $result = supabaseQuery('users', [
        'select' => '*',
        'email' => 'eq.' . $email
    ]);
    
    if ($result['success'] && count($result['data']) > 0) {
        return $result['data'][0];
    }
    
    return null;
}

// Fungsi untuk mendapatkan user by id_pengguna
function getUserById($id)
{
    $result = supabaseQuery('pengguna', [
        'select' => '*',
        'id_pengguna' => 'eq.' . $id
    ]);
    
    if ($result['success'] && count($result['data']) > 0) {
        return $result['data'][0];
    }
    
    return null;
}
?>