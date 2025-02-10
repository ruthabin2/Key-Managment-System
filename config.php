<?php
// config.php

// Generate the key pair (if not already generated)
if (!file_exists('private_key.pem') || !file_exists('public_key.pem')) {
    $config = array(
        "digest_alg" => "sha512",
        "private_key_bits" => 4096,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    );

    $res = openssl_pkey_new($config);

    openssl_pkey_export($res, $private_key);
    file_put_contents('private_key.pem', $private_key);

    $public_key = openssl_pkey_get_details($res);
    $public_key = $public_key["key"];
    file_put_contents('public_key.pem', $public_key);
}

// Load keys
$private_key = file_get_contents('private_key.pem');
$public_key = file_get_contents('public_key.pem');
?>
