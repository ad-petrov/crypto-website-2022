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

//Get p, q, e from webform, count params
$p = $_POST['p']; $q = $_POST['q']; $e = $_POST['e'];
$n = $p * $q;
$fi_n = ($p-1) * ($q-1);

//count secret_key (Euclid 's algorithm)
$m=$e; $r=$fi_n; $i=0; $qa = array(); $qq[0] = 1;
while($r > 0){
    $qa[$i] = intdiv($m, $r);
    $i++;
    $tmp = $m % $r;
    $m = $r;
    $r = $tmp;
}

$qq[1] = $qa[1];

for ($i=2; $i<count($qa)-1; $i++) {
   $qq[$i] = $qq[$i-1]*$qa[$i] + $qq[$i-2];
}

$d = $qq[count($qq)-1];
if ($e * $d % $fi_n != 1) $d = $fi_n - $d;
echo $d;

//Send public key (e, n) to public database
$conn_public = new mysqli($servername, $public_uname, $public_dbpass, $public_dbname);
$sql_send_public_key = "INSERT INTO rsa (id, e, n)
VALUES (1, $e, $n)";
$conn_public->query($sql_send_public_key);

//Save params in client database
$conn_client = new mysqli($servername, $username, $password, $dbname);
$sql_save_client_params = "INSERT INTO rsa (id, p, q, n, e, fi_n, secret)
VALUES (1, $p, $q, $n, $e, $fi_n, $d)";
$conn_client->query($sql_save_client_params);