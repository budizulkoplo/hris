<?php
// Koneksi DB
include 'koneksidb.php';

// Ambil data device (anggap hanya 1 device aktif)
$sqldev = mysqli_query($conn,"SELECT * FROM tb_device");
if(!$sqldev || mysqli_num_rows($sqldev) == 0){
    die("⚠️ Tidak ada device terdaftar di tb_device");
}
$datadev = mysqli_fetch_assoc($sqldev);

// Fungsi webservice
function webservice($port,$url,$parameter){
    $curl = curl_init();
    set_time_limit(0);
    curl_setopt_array($curl, array(
        CURLOPT_PORT => $port,
        CURLOPT_URL => "http://".$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $parameter,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return "Error #:" . $err;
    }
    return $response;
}

// Ambil parameter device
$sn   = $datadev['device_sn'];
$port = $datadev['server_port'];
$url  = $datadev['server_IP']."/scanlog/new";
$parameter = "sn=".$sn;

// Panggil API mesin
$server_output = webservice($port,$url,$parameter);

// Validasi respon
if (empty($server_output)) {
    echo date("Y-m-d H:i:s")." ⚠️ Tidak ada respon dari mesin\n";
    exit;
}

$content = json_decode($server_output);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo date("Y-m-d H:i:s")." ⚠️ JSON decode error: ".json_last_error_msg()."\n";
    echo $server_output."\n";
    exit;
}

// Proses data jika ada
if ($content && isset($content->Result) && $content->Result == true && !empty($content->Data)) {
    foreach($content->Data as $entry){
        $Jsn  = trim($entry->SN);
        $Jsd  = trim($entry->ScanDate);
        $Jpin = mysqli_real_escape_string($conn, trim($entry->PIN));
        $Jvm  = (int)$entry->VerifyMode;
        $Jim  = (int)$entry->IOMode;
        $Jwc  = (int)$entry->WorkCode;

        $sqlinsertscan = "INSERT INTO tb_scanlog 
            (sn, scan_date, pin, verifymode, iomode, workcode) 
            VALUES ('$Jsn','$Jsd','$Jpin',$Jvm,$Jim,$Jwc)";

        if (!mysqli_query($conn,$sqlinsertscan)) {
            echo date("Y-m-d H:i:s")." ❌ Insert gagal: ".mysqli_error($conn)."\n";
        } else {
            echo date("Y-m-d H:i:s")." ✅ Insert OK: $Jpin @ $Jsd\n";
        }
    }
} else {
    echo date("Y-m-d H:i:s")." ⚠️ No new data / request failed.\n";
    echo $server_output."\n";
}
?>
