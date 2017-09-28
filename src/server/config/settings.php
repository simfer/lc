<?php
return array(
  'jwt' => array(
    'key'       => '4b49DFiABfJ7z65tccFdNBNuOKAzK8Bh1vAiRrNBkQSVwn4W4ynszwTqRCCQYm4PvixW5zfdij03y+UMagDIhg==',     // Key for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
    'algorithm' => 'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
  ),
  'database' => array(
    'user'     => 'root', // Database username
    'password' => 'Admin123', // Database password
    'host'     => 'localhost', // Database host
    'name'     => 'lcdb', // Database schema name
  ),
  'serverName' => 'http://localhost:8080/lovechallenge/server',
);
