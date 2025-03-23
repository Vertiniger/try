<?php
$file = "traffic.json";

// Jika file belum ada, buat file kosong
if (!file_exists($file)) {
    file_put_contents($file, json_encode(["total_visits" => 0, "unique_visits" => 0, "visitors" => []]));
}

// Ambil data dari file JSON
$data = json_decode(file_get_contents($file), true);

// Ambil informasi pengunjung
$ip = $_SERVER["REMOTE_ADDR"];
$user_agent = $_SERVER["HTTP_USER_AGENT"];
$time = date("Y-m-d H:i:s");

// Cek lokasi IP menggunakan API ip-api.com
$geo_info = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);
$country = ($geo_info && isset($geo_info["country"])) ? $geo_info["country"] : "Unknown";

// Cek apakah IP sudah tercatat sebelumnya
$ip_exists = false;
foreach ($data["visitors"] as $visitor) {
    if ($visitor["ip"] === $ip) {
        $ip_exists = true;
        break;
    }
}

// Jika IP baru, tambahkan sebagai kunjungan unik
if (!$ip_exists) {
    $data["unique_visits"] += 1;
}

// Tambah total kunjungan (termasuk kunjungan berulang)
$data["total_visits"] += 1;

// Simpan data baru
$data["visitors"][] = [
    "ip" => $ip,
    "user_agent" => $user_agent,
    "time" => $time,
    "country" => $country
];

// Simpan ke file JSON
file_put_contents($file, json_encode($data));

echo "OK";
?>
