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

$conn_server = new mysqli($servername, $username, $password, $dbname);
$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);

$sql_drop = "TRUNCATE diffie_hellman";
$conn_server->query($sql_drop);
$conn_public->query($sql_drop);

$sql_drop = "TRUNCATE rsa";
$conn_server->query($sql_drop);
$conn_public->query($sql_drop);

$sql_drop = "TRUNCATE message";
$conn_server->query($sql_drop);
$conn_public->query($sql_drop);