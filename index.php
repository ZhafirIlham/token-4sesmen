<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// KUNCI RAHASIA (Pastikan sama dengan yang ada di validasi_token.php)
$secret_key = "SMKN4BDG_4SESMEN_2026_SECRET";

// Konfigurasi durasi masing-masing token dalam satuan detik
$interval_utama = 600; // 10 menit (600 detik)
$interval_kunci = 10;  // 10 detik

$now = time();

// Menghitung blok waktu independen untuk masing-masing token
$slot_utama = floor($now / $interval_utama);
$slot_kunci = floor($now / $interval_kunci);

// Menghitung sisa waktu sebelum masing-masing token berganti
$sisa_detik_utama = $interval_utama - ($now % $interval_utama);
$sisa_detik_kunci = $interval_kunci - ($now % $interval_kunci);

// Fungsi deterministik penghasil 6 karakter huruf kapital (tanpa I dan O agar jelas dibaca siswa)
function generate_deterministic_token($seed, $secret) {
    $hash = hash_hmac('sha256', (string)$seed, $secret);
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $token = '';
    for ($i = 0; $i < 6; $i++) {
        $val = hexdec(substr($hash, $i * 2, 2));
        $token .= $chars[$val % strlen($chars)];
    }
    return $token;
}

$response = [
    "status" => "success",
    "server_time_utc" => gmdate("Y-m-d\TH:i:s\Z", $now),
    
    // Konfigurasi & Token Utama (Berganti setiap 10 menit)
    "token_utama" => [
        "kode" => generate_deterministic_token("UTAMA_" . $slot_utama, $secret_key),
        "berlaku_detik" => $interval_utama,
        "sisa_waktu_detik" => $sisa_detik_utama,
        "prev_kode" => generate_deterministic_token("UTAMA_" . ($slot_utama - 1), $secret_key)
    ],

    // Konfigurasi & Token Kunci (Berganti setiap 10 detik)
    "token_kunci" => [
        "kode" => generate_deterministic_token("KUNCI_" . $slot_kunci, $secret_key),
        "berlaku_detik" => $interval_kunci,
        "sisa_waktu_detik" => $sisa_detik_kunci,
        "prev_kode" => generate_deterministic_token("KUNCI_" . ($slot_kunci - 1), $secret_key)
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
