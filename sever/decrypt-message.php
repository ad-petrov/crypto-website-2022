<?php

//Server database config
$servername = "localhost";
$dbname = "apetrov_server";
$username = "apetrov_server";
$password = "RP1Bs*TPHCV5";

//Public database config
$public_uname = "apetrov_public";
$public_dbname = "apetrov_public";
$public_dbpass = "NF7X822e#Y5F";

//get AES key
$conn_server = new mysqli($servername, $username, $password, $dbname);
$sql_get_AES_key = "SELECT K FROM diffie_hellman WHERE id = 1";
$AES_params = $conn_server->query($sql_get_AES_key);
$aes_key;
while($row = $AES_params->fetch_assoc()) {
    $aes_key = $row["K"];
  }

//get rsa public key
$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);
$sql_RSA_keys = "SELECT e, n FROM rsa WHERE id = 1";
$RSA_keys = $conn_public->query($sql_RSA_keys); $e; $n;
while($row = $RSA_keys->fetch_assoc()) {
    $e = $row["e"];
    $n = $row["n"];
}
echo "AES secret key: $aes_key<br>";
echo "RSA public key: ($e, $n)<br>";

//get message and signature
$sql_get_message = "SELECT text, signature FROM message WHERE id = 1";
$message = $conn_public->query($sql_get_message);
while($row = $message->fetch_assoc()) {
    $encrypted_message = $row["text"];
    $encrypted_signature = $row["signature"];
}
echo "Encrypted text: $encrypted_message<br>";
echo "Encrypted Signature: $encrypted_signature<br>";

exec("../aes.py $aes_key '$encrypted_message' 2", $output);
$decrypted_message = $output[0];
echo "Decrypted message: $decrypted_message <br>";

exec("../aes.py $aes_key '$encrypted_signature' 2", $output2);
$decrypted_signature = $output2[0];
echo "Decrypted signature: $decrypted_signature <br>";

//decrypt signature
$enc_hash = explode("^", $decrypted_signature);
print_r($enc_hash); echo "<br>";
$dec_hash = "";

for ($i =0; $i < count($enc_hash); $i++){
    $tmp = bcpowmod($enc_hash[$i], $e, $n);
    $dec_hash = $dec_hash.chr($tmp);
}
echo "$dec_hash <br>";

//check signature
echo "$decrypted_message <br>";
echo md5($decrypted_message)."<br>";

if (md5($decrypted_message) == $dec_hash) {
    echo "Success";
}
else{
    echo "Fail";
}