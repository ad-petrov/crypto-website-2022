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

//Get secret key b
$b = $_POST['b'];

//Get A, p, g from public database
$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);
$sql_get_DH_params = "SELECT A, p, g FROM diffie_hellman WHERE id = 1";
$DH_params = $conn_public->query($sql_get_DH_params);
$A; $p; $g;
while($row = $DH_params->fetch_assoc()) {
    $A = $row["A"];
    $p = $row["p"];
    $g = $row["g"];
  }
  
//Count public and secret keys
$public_key = bcpowmod($g, $b, $p);
$secret_key = bcpowmod($A, $b, $p);

//Send public key (B) to public database
$sql_send_public_key = "UPDATE diffie_hellman SET B = $public_key where id = 1";
$conn_public->query($sql_send_public_key);

//Save params in client database
$conn_client = new mysqli($servername, $username, $password, $dbname);
$sql_save_client_params = "INSERT INTO diffie_hellman (id, p, g, secret, B, K)
VALUES (1, $p, $g, $b, $public_key, $secret_key)";
$conn_client->query($sql_save_client_params);