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

//get B
$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);
$sql_get_B = "SELECT B FROM diffie_hellman WHERE id = 1";
$Bsql = $conn_public->query($sql_get_B); $B;
while($row = $Bsql->fetch_assoc()) {
    $B = $row["B"];
  }
  
//get a, p
$conn_server = new mysqli($servername, $username, $password, $username);
$sql_get_params = "SELECT secret, p FROM diffie_hellman WHERE id = 1";
$asql = $conn_server->query($sql_get_params); $a; $p;
while($row = $asql->fetch_assoc()) {
    $a = $row["secret"];
    $p = $row["p"];
  }
  
echo bcmod(bcpow(501, 228), 839);

//count and save common secret key K
$secret_key = bcmod(bcpow($B, $a), $p);
$sql_save_key =  "UPDATE diffie_hellman SET K = $secret_key, B = $B where id = 1";
$conn_server->query($sql_save_key);