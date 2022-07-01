<?php

// Client database config
$servername = "localhost";
$dbname = "apetrov_client";
$username = "apetrov_client";
$password = "VP2BNJHBK1n2";

//Public database config
$public_uname = "apetrov_public";
$public_dbname = "apetrov_public";
$public_dbpass = "NF7X822e#Y5F";

//get message text, hash, hash to array
$msg = $_POST['msg'];
$msghash = md5($msg);

echo "hash is $msghash <br>";

//get RSA params
$conn_client = new mysqli($servername, $username, $password, $dbname);
$sql_get_RSA_params = "SELECT n, secret FROM rsa WHERE id = 1";
$RSA_params = $conn_client->query($sql_get_RSA_params);
$n; $d;
while($row = $RSA_params->fetch_assoc()) {
    $n = $row["n"];
    $d = $row["secret"];
  }

//get AES key
$sql_get_AES_key = "SELECT K FROM diffie_hellman WHERE id = 1";
$AES_params = $conn_client->query($sql_get_AES_key);
$aes_key;
while($row = $AES_params->fetch_assoc()) {
    $aes_key = $row["K"];
  }
  
// encrypt hash using RSA - create signature
$enc_hash = array();
for ($i = 0; $i < strlen($msghash); $i++) {
    $lt = ord($msghash[$i]);
	$enc_hash[$i] = bcpowmod($lt, $d, $n);
    echo "$msghash[$i] goes into $lt, it goes into $enc_hash[$i] <br>";
}

$signature=$enc_hash[0];
for ($i = 1; $i < count($enc_hash); $i++) {
    $signature = $signature."^".$enc_hash[$i];
}
echo $signature."<br>";

exec("../aes.py $aes_key $msg 1", $output);
echo "Encrypted mas: $output[0] <br>";
$encrypted_message = "";
for($i =1; $i < strlen($output[0]); $i++){
    if ($output[0][$i] != "'"){
        $encrypted_message = $encrypted_message.$output[0][$i];
    }
}
echo "Encrypted text: $encrypted_message <br>";

$encrypted_message = str_replace("\\", "\\\\", $encrypted_message);

exec("../aes.py $aes_key $signature 1", $output2);
echo "Encrypted mas: $output2[0] <br>";
$encrypted_signature = "";
for($i =1; $i < strlen($output2[0]); $i++){
    if ($output2[0][$i] != "'"){
        $encrypted_signature = $encrypted_signature.$output2[0][$i];
    }
}
echo "Encrypted signature: $encrypted_signature <br>";

$encrypted_signature = str_replace("\\", "\\\\", $encrypted_signature);

// save msg + signature (no encryption)
$conn_client = new mysqli($servername, $username, $password, $dbname);
$sql_save_msg = "INSERT INTO message (id, text, signature)
VALUES (1, '$msg', '$signature')";

if ($conn_client->query($sql_save_msg) === TRUE) {
  echo "<br>Message saved successfully.<br>";
} else {
  echo "Error: " . $sql_save_msg . "<br>" . $conn_client->error;
}

// Send msg + signature to public (encrypted)
$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);
$sql_send_msg = "INSERT INTO message (id, text, signature)
VALUES (1,  '$encrypted_message', '$encrypted_signature')";

if ($conn_public->query($sql_send_msg) === TRUE) {
  echo "<br>Message sent successfully.<br>";
} else {
  echo "Error: " . $sql_send_msg . "<br>" . $conn_public->error;
}