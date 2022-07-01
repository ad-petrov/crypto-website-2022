<?php

//Server database config
$servername = "localhost";
$dbname = "apetrov_server";
$username = "apetrov_server";
$password = "RP1Bs*TPHCV5";

//Public database config
$public_ubname = "apetrov_public";
$public_dbname = "apetrov_public";
$public_dbpass = "NF7X822e#Y5F";

//Get a, p, g from webform, count public key
$a = $_POST['a']; $p = $_POST['p']; $g = $_POST['g']; 
$public_key = bcmod(bcpow($g, $a), $p);

// Save DH params in server database
$conn_server = new mysqli($servername, $username, $password, $dbname);
$sql_save_DH_params = "INSERT INTO diffie_hellman (id, p, g, secret, A)
VALUES (1, $p, $g, $a, $public_key)";
$conn_server->query($sql_save_DH_params);

// publish parameters
$conn_public = new mysqli($servername, $public_ubname, $public_dbpass, $public_dbname);
$sql_send_public_params = "INSERT INTO diffie_hellman (id, A, p, g)
VALUES (1, $public_key, $p, $g)";
$conn_public ->query($sql_send_public_params);