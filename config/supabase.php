<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

// Masukkan kredensial Supabase
$supabase_url = 'https://tkjnbelcgfwpbhppsnrl.supabase.co';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRram5iZWxjZ2Z3cGJocHBzbnJsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjE3NDA3NjIsImV4cCI6MjA3NzMxNjc2Mn0.wOjK4X2qJV6LzOG4yXxnfeTezDX5_3Sb3wezhCuQAko';

// Buat koneksi HTTP ke Supabase
$client = new Client([
    'base_uri' => $supabase_url . '/rest/v1/',
    'headers' => [
        'apikey' => $supabase_key,
        'Authorization' => 'Bearer ' . $supabase_key,
        'Content-Type' => 'application/json',
    ],
    'http_errors' => false
]);

// Fungsi helper untuk query Supabase
function supabaseQuery($client, $table, $params = [])
{
    try {
        $response = $client->get($table, [
            'query' => $params
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data' => $data,
            'count' => $response->getHeader('Content-Range')[0] ?? null,
            'status' => $statusCode
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'data' => [],
            'status' => 500
        ];
    }
}

// Fungsi helper untuk insert data ke Supabase
function supabaseInsert($client, $table, $data)
{
    try {
        $response = $client->post($table, [
            'json' => $data,
            'headers' => [
                'Prefer' => 'return=representation'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $result = json_decode($body, true);

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data' => $result,
            'status' => $statusCode
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'data' => [],
            'status' => 500
        ];
    }
}
