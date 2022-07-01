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

$conn_server = new mysqli($servername, $username, $password, $dbname);
$sql_drop = "TRUNCATE diffie_hellman";
$conn_server->query($sql_drop);

$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);
$conn_public->query($sql_drop);