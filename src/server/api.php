<?php

define('AUTHORIZATION_CHECK_DISABLED', true);
define('SERVER_HOSTNAME','http://localhost:8080/lovechallenge');

require_once('vendor/autoload.php');

use Zend\Config\Factory;
use Zend\Http\PhpEnvironment\Request;
use Zend\Json;
use Firebase\JWT\JWT;

$config = Factory::fromFile('config/settings.php', true);
$dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

// this is to be removed and adjusted
$loggedUser = 1;

// get a new request
$request = new Request();

//echo $request->getQuery()->regions;
//die();

$request_parts = explode('/', $request->getQuery("url"));
$object = (isset($request_parts[0]) ? $request_parts[0] : ''); //the first piece is the object
$element = (isset($request_parts[1]) ? $request_parts[1] : ''); // the second is the object's element on which we want to perform the operation


switch(strtolower($object)) {
    case 'test':
        $res = array('prova'=>'riuscita');

        header('Content-type: application/json');
        echo json_encode($res);

        break;
    case 'confirmregistration':
        if (checkAuthorization($request)) {
            if ($request->isGet()) {
                if ($element) {
                    if ($request->getQuery()->tokenid) {
                        $passedTokenId = $request->getQuery()->tokenid;
                        try {
                            $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $SQL = 'SELECT * FROM customers WHERE idcustomer = ?';
                            $stmt = $conn->prepare($SQL);
                            $stmt->execute([$element]);
                            $rs = $stmt->fetch();
                            if ($rs) { // if a record is found
                                $registrationtoken = $rs['registrationtoken'];
                                $active = $rs['active'];
                                $registered = $rs['registered'];
                                if ($registrationtoken && $active == 1 && $registered == 0) {
                                    if (urldecode($registrationtoken) == $passedTokenId) {
                                        try {
                                            $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                            $SQLREGISTER = "UPDATE customers SET registrationtoken = null, registered = 1 WHERE idcustomer = " . $element;
                                            $conn->beginTransaction();
                                            $stmt = $conn->prepare($SQLREGISTER);
                                            $stmt->execute();
                                            $conn->commit();

                                            //$res = array('tokenid'=>urldecode($registrationtoken),'passedtokenid'=>$passedTokenId);
                                            //header('Content-type: application/json');
                                            //echo json_encode($res);
                                            echo 'Registratione confermata! Fai click <a href="http://localhost:4200/login">qui</a> per accedere con le tue credenziali!';

                                        } catch (Exception $e) {
                                            header('HTTP/1.0 500 Internal Server Error');
                                            echo $e->getMessage();
                                        }
                                    } else {
                                        header('HTTP/1.0 400 Bad Request');
                                        echo "Invalid Token ID!";
                                    }
                                } else {
                                    header('HTTP/1.0 400 Bad Request');
                                    echo "Utente già registrato!";
                                }
                            } else { // user is not found
                                header('HTTP/1.0 404 Not Found');
                                echo 'User Not Found';
                            }
                        } catch (Exception $e) {
                            header('HTTP/1.0 500 Internal Server Error');
                            echo $e->getMessage();
                        }
                    } else {
                        header('HTTP/1.0 400 Bad Request');
                        echo "Token ID not specified!";
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Customer id not specified!";
                }
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }
        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'userlogin':
        if ($request->isPost()) {
            $body = $request->getContent();
            if (!empty($body)) {
                $data = Json\Json::decode($body,true);
                $username = $data['username'];
                $password = $data['password'];
                if ($username && $password) {
                    try {

                        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql ='SELECT * FROM users WHERE  username = ?';

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$username]);
                        $rs = $stmt->fetch();

                        if ($rs) { // if a record is found
                            if (password_verify($password, $rs['password'])) { // if the password is correct
                                $tokenId    = base64_encode(random_bytes(32));
                                $issuedAt   = time();
                                $notBefore  = $issuedAt;  //Adding 0 seconds
                                $expire     = $notBefore + 3600;
                                $serverName = $config->get('serverName');

                                $data = [
                                    'iat'  => $issuedAt,         // Issued at: time when the token was generated
                                    'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                                    'iss'  => $serverName,       // Issuer
                                    'nbf'  => $notBefore,        // Not before
                                    'exp'  => $expire,           // Expire
                                    'data' => [                  // Data related to the signer user
                                        'userId'   => $rs['iduser'], // userid from the users table
                                        'userName' => $username, // User name
                                    ]
                                ];
                                $secretKey = base64_decode($config->get('jwt')->get('key'));

                                // Extract the algorithm from the config file too
                                $algorithm = $config->get('jwt')->get('algorithm');

                                // Encode the array to a JWT string.
                                $jwt = JWT::encode(
                                    $data,      //Data to be encoded in the JWT
                                    $secretKey, // The signing key
                                    $algorithm  // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
                                );

                                $unencodedArray = [
                                    'iduser' => $rs['iduser'],
                                    'username' => $username,
                                    'jwt' => $jwt]; // return the JWT code

                                header('Content-type: application/json');
                                echo json_encode($unencodedArray);
                            } else { // password is not correct
                                header('HTTP/1.0 401 Unauthorized');
                                echo 'HTTP/1.0 401 Unauthorized';
                            }
                        } else { // user is not found
                            header('HTTP/1.0 404 Not Found');
                            echo 'HTTP/1.0 404 Not Found';
                        }
                    } catch (Exception $e) {
                        header('HTTP/1.0 500 Internal Server Error');
                        echo $e->getMessage();
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo 'Invalid user and/or password';
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "Request body is empty!";
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "Method Not Allowed!";
        }
        break;
    case 'customerlogin':
        if ($request->isPost()) {
            $body = $request->getContent();
            if (!empty($body)) {
                $data = Json\Json::decode($body,true);
                $username = $data['username'];
                $password = $data['password'];
                if ($username && $password) {
                    try {
                        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql ='SELECT * FROM customers WHERE  username = ?';

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$username]);
                        $rs = $stmt->fetch();

                        if ($rs) { // if a record is found
                            if (password_verify($password, $rs['password'])) { // if the password is correct
                                $tokenId    = base64_encode(random_bytes(32));
                                $issuedAt   = time();
                                $notBefore  = $issuedAt;  //Adding 0 seconds
                                $expire     = $notBefore + 604800; // Adding 60 seconds
                                $serverName = $config->get('serverName');

                                $data = [
                                    'iat'  => $issuedAt,         // Issued at: time when the token was generated
                                    'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                                    'iss'  => $serverName,       // Issuer
                                    'nbf'  => $notBefore,        // Not before
                                    'exp'  => $expire,           // Expire
                                    'data' => [                  // Data related to the signer user
                                        'userId'   => $rs['idcustomer'], // userid from the users table
                                        'userName' => $username, // User name
                                    ]
                                ];
                                $secretKey = base64_decode($config->get('jwt')->get('key'));

                                // Extract the algorithm from the config file too
                                $algorithm = $config->get('jwt')->get('algorithm');

                                // Encode the array to a JWT string.
                                $jwt = JWT::encode(
                                    $data,      //Data to be encoded in the JWT
                                    $secretKey, // The signing key
                                    $algorithm  // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
                                );

                                $unencodedArray = [
                                    'idcustomer' => $rs['idcustomer'],
                                    'username' => $username,
                                    'registered' => $rs['registered'],
                                    'subscribed' => $rs['subscribed'],
                                    'jwt' => $jwt]; // return the JWT code

                                header('Content-type: application/json');
                                echo json_encode($unencodedArray);
                            } else { // password is not correct
                                header('HTTP/1.0 401 Unauthorized');
                                echo 'HTTP/1.0 401 Unauthorized';
                            }
                        } else { // user is not found
                            header('HTTP/1.0 404 Not Found');
                            echo 'HTTP/1.0 404 Not Found';
                        }
                    } catch (Exception $e) {
                        header('HTTP/1.0 500 Internal Server Error');
                        echo $e->getMessage();
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo 'Invalid user and/or password';
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "Request body is empty!";
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "Method Not Allowed!";
        }
        break;
    case 'customerregister':
        if ($request->isPost()) {
            $body = $request->getContent();
            if (!empty($body)) {
                $data = Json\Json::decode($body,true);

                $fields = "";
                $values = "";

                $SQL = "INSERT INTO customers (";
                $registrationtoken = urldecode(base64_encode(random_bytes(32)));
                $emailRecipient = $data["email"];
                $data["registrationtoken"] = $registrationtoken;
                $data["changedby"] = $loggedUser;
                $data["operation"] = 'I';

                foreach ($data as $k => $v) {
                    if ($k =="password") {
                        $v = password_hash($v,PASSWORD_DEFAULT);
                    }
                    $fields .= $k . ",";
                    if ($v == "NULL") {
                        $values .= $v . ",";
                    } else {
                        $values .= "'" . addslashes($v) . "',";
                    }
                }
                $fields .= "changedat";
                $values .= "SYSDATE()";
                $SQL .= $fields . ") VALUES (" . $values . ")";

                try {
                    $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    try {
                        $conn->beginTransaction();
                        $stmt = $conn->prepare($SQL);
                        $stmt->execute();
                        $res = array('lastInsertID'=>$conn->lastInsertId(),'registrationtoken'=>$registrationtoken);
                        $idcustomer = $conn->lastInsertId();
                        $conn->commit();

                        // send registration email
                        $to = $emailRecipient;
                        $subject = 'Love Challenge - Conferma registrazione!';
                        $message = 'Fai click <a href="' . SERVER_HOSTNAME . '/server/api/v1/confirmregistration/' . $idcustomer . '?tokenid=' . $registrationtoken . '">qui</a> per confermare la tua registrazione!';
                        $headers = 'From: webmaster@example.com' . "\r\n" .
                            'Reply-To: webmaster@example.com' . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();

                        mail($to, $subject, $message, $headers);

                        header('Content-type: application/json');
                        echo json_encode($res);
                    } catch(PDOExecption $e) {
                        $conn->rollback();
                        header('HTTP/1.0 400 Bad Request');
                        echo $e->getMessage();
                    }
                    $conn = null;
                } catch (Exception $e) {
                    header('HTTP/1.0 500 Internal Server Error');
                    echo $e->getMessage();
                }

            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "Request body is empty!";
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "Method Not Allowed!";
        }
        break;
    case 'customersubscribe':
        if (checkAuthorization($request)) { // if the request is valid

            if ($request->isPut()) {
                if ($element == '') {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Key value is not specified!";
                } else {
                    $SQL = "UPDATE customers SET subscribed = 1,";
                    $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idcustomer = " . $element;

                    try {
                        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        try {
                            $conn->beginTransaction();
                            $stmt = $conn->prepare($SQL);
                            $stmt->execute();
                            $conn->commit();
                            //$res = ['SQL' => $SQL];
                            $SQL = "SELECT * FROM customers WHERE idcustomer = " . $element;
                            $stmt = $conn->prepare($SQL);
                            $stmt->execute();
                            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch(PDOExecption $e) {
                            $conn->rollback();
                            header('HTTP/1.0 400 Bad Request');
                            echo $e->getMessage();
                        }
                        $conn = null;
                    } catch (Exception $e) {
                        header('HTTP/1.0 500 Internal Server Error');
                        echo $e->getMessage();
                    }

                    header('Content-type: application/json');
                    echo json_encode($res);

                }
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'sendmail':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                if ($element) {
                    try {
                        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $SQL ='SELECT * FROM customers WHERE active = 1 AND registered = 0 AND idcustomer = ?';

                        $stmt = $conn->prepare($SQL);
                        $stmt->execute([$element]);
                        $rs = $stmt->fetch();

                        if ($rs) { // if a record is found
                            $emailRecipient = $rs['email'];
                            $idcustomer = $element;
                            $registrationtoken = $rs['registrationtoken'];

                            $res = array('customer_email'=>$rs['email']);
                            //send email
                            $to = $emailRecipient;
                            $subject = 'Love Challenge - Conferma registrazione!';
                            $message = 'Fai click <a href="' . SERVER_HOSTNAME . '/server/api/v1/confirmregistration/' . $idcustomer . '?tokenid=' . $registrationtoken . '">qui</a> per confermare la tua registrazione!';
                            $headers = 'From: webmaster@example.com' . "\r\n" .
                                'Reply-To: webmaster@example.com' . "\r\n" .
                                'X-Mailer: PHP/' . phpversion();
                            mail($to, $subject, $message, $headers);
                            $res = array('email'=>$emailRecipient);
                            header('Content-type: application/json');
                            echo json_encode($res);

                        } else { // user is not found
                            header('HTTP/1.0 404 Not Found');
                            echo 'User Not Found';
                        }
                    } catch (Exception $e) {
                        header('HTTP/1.0 500 Internal Server Error');
                        echo $e->getMessage();
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Customer id not specified!";
                }
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }
        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'unregisteredusers':
        if ($request->isGet()) {
            $request_parts = explode('&', $request->getUriString());
            $tokenUrl = $request_parts[1];
            $tokenId = str_replace('token=', '', $tokenUrl);

            $SQL = "SELECT * FROM customers WHERE active = 0 AND registrationtoken = '" . $tokenId ."'";
            $data = executeSelect($SQL);
            header('Content-type: application/json');
            echo json_encode($data);
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "Method Not Allowed!";
        }
        break;
    case 'checkexistingusername':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body, true);
                    $username = $data['username'];

                    if ($username) {
                        try {
                            $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $sql = 'SELECT * FROM customers WHERE  username = ?';

                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$username]);
                            $rs = $stmt->fetch();

                            if ($rs) { // if a record is found
                                $res = array('found'=>true);
                            } else {
                                $res = array('found'=>false);
                            }
                        } catch (PDOExecption $e) {
                            header('HTTP/1.0 400 Bad Request');
                            echo $e->getMessage();
                        }
                        $conn = null;
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
                header('Content-type: application/json');
                echo json_encode($res);
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'users':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM users WHERE active = 1";
                if ($element) {
                    $SQL .= " AND iduser = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            } elseif ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }

                $fields = "";
                $values = "";

                $SQL = "INSERT INTO " . $object . " (";
                foreach ($data as $k => $v) {
                    if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                    $fields .= $k . ",";
                    if ($v == "NULL") $values .= $v . ",";
                    else $values .= "'" . addslashes($v) . "',";
                }

                $fields .= "active,changedby,operation,changedat";
                $values .= "'1','" . $loggedUser . "','I',SYSDATE()";

                $SQL .= $fields . ") VALUES (" . $values . ")";

                $res = executeInsert($SQL);

                header('Content-type: application/json');
                echo json_encode($res);


            } elseif ($request->isPut()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                    if ($element == '') {
                        header('HTTP/1.0 400 Bad Request');
                        echo "Key value is not specified!";
                    } else {
                        $fields = "";
                        $values = "";

                        $SQL = "UPDATE " . $object . " SET ";
                        foreach ($data as $k => $v) {
                            if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                            $field = $k . " = ";
                            if ($v != "NULL") $field .= "'" . addslashes($v) . "',";
                            $SQL .= $field;
                        }

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE iduser = " . $element;

                        $res = executeUpdate($SQL, $object, 'iduser', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE users SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND iduser = '" . $element . "'";
                }
                $data = executeDelete($SQL);
                header('Content-type: application/json');
                echo json_encode($data);

            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'customers':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM customers WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idcustomer = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            } elseif ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }

                $fields = "";
                $values = "";

                $SQL = "INSERT INTO " . $object . " (";
                foreach ($data as $k => $v) {
                    if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                    $fields .= $k . ",";
                    if ($v == "NULL") $values .= $v . ",";
                    else $values .= "'" . addslashes($v) . "',";
                }

                $fields .= "active,changedby,operation,changedat";
                $values .= "'1','" . $loggedUser . "','I',SYSDATE()";

                $SQL .= $fields . ") VALUES (" . $values . ")";

                $res = executeInsert($SQL);

                header('Content-type: application/json');
                echo json_encode($res);


            } elseif ($request->isPut()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                    if ($element == '') {
                        header('HTTP/1.0 400 Bad Request');
                        echo "Key value is not specified!";
                    } else {
                        $fields = "";
                        $values = "";

                        $SQL = "UPDATE " . $object . " SET ";
                        foreach ($data as $k => $v) {
                            if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                            $field = $k . " = ";
                            if ($v != "NULL") $field .= "'" . addslashes($v) . "',";
                            $SQL .= $field;
                        }

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idcustomer = " . $element;

                        $res = executeUpdate($SQL, $object, 'idcustomer', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE customers SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idcustomer = '" . $element . "'";
                }
                $data = executeDelete($SQL);
                header('Content-type: application/json');
                echo json_encode($data);

            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'products':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM products WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idproduct = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            } elseif ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }

                $fields = "";
                $values = "";

                $SQL = "INSERT INTO " . $object . " (";
                foreach ($data as $k => $v) {
                    if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                    $fields .= $k . ",";
                    if ($v == "NULL") $values .= $v . ",";
                    else $values .= "'" . addslashes($v) . "',";
                }

                $fields .= "active,changedby,operation,changedat";
                $values .= "'1','" . $loggedUser . "','I',SYSDATE()";

                $SQL .= $fields . ") VALUES (" . $values . ")";

                $res = executeInsert($SQL);

                header('Content-type: application/json');
                echo json_encode($res);


            } elseif ($request->isPut()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                    if ($element == '') {
                        header('HTTP/1.0 400 Bad Request');
                        echo "Key value is not specified!";
                    } else {
                        $fields = "";
                        $values = "";

                        $SQL = "UPDATE " . $object . " SET ";
                        foreach ($data as $k => $v) {
                            if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                            $field = $k . " = ";
                            if ($v != "NULL") $field .= "'" . addslashes($v) . "',";
                            $SQL .= $field;
                        }

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idproduct = " . $element;

                        $res = executeUpdate($SQL, $object, 'idproduct', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE products SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idproduct = '" . $element . "'";
                }
                $data = executeDelete($SQL);
                header('Content-type: application/json');
                echo json_encode($data);

            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'provinces':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM provinces WHERE 1=1";
                if ($element) {
                    $SQL .= " AND idprovince = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'orders':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                //$SQL = "SELECT * FROM orders WHERE active = 1";
                $SQL = "select a.*,CONCAT(b.lastname,' ',b.firstname) as customer,
                  c.description as product, d.description as category,
                  e.description as status
                  from orders a
                  inner join customers b on a.idcustomer=b.idcustomer
                  inner join products c on a.idproduct = c.idproduct
                  inner join categories d on a.idcategory = d.idcategory
                  inner join statuses e on a.idstatus = e.idstatus
                  where a.active=1";

                if ($element) { // here I'm searching for a specific order ID
                    $SQL .= " AND a.idorder = '" . $element . "'";
                } else { // if no order ID is specified I check if another parameter like idstatus has been specified
                    if ($request->getQuery()->idstatus) {
                        $SQL .= " AND a.idstatus = '" . $request->getQuery()->idstatus . "'";
                    }
                    if ($request->getQuery()->idproduct) {
                        $SQL .= " AND a.idproduct = '" . $request->getQuery()->idproduct . "'";
                    }
                }


                $SQL .= " order by idorder desc";
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            }
            elseif ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body,true);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }

                $SQL = "INSERT INTO orders (idcustomer,idproduct,quantity,amount,orderdate,active,changedby,operation,changedat) VALUES(";
                $SQL .= $data["idcustomer"] . ",";
                $SQL .= $data["idproduct"] . ",";
                $SQL .= $data["quantity"] . ",";
                $SQL .= $data["amount"] . ",";
                $SQL .= "'". $data["orderdate"] . "',";
                $SQL .= "'1','" . $loggedUser . "','I',SYSDATE())";

                //$data["sql"] = $SQL;

                //header('Content-type: application/json');
                //echo json_encode($data);
                //die();

                $res = null;
                try {
                    $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    try {
                        $conn->beginTransaction();
                        $stmt = $conn->prepare($SQL);
                        $stmt->execute();
                        $lastInsertID = $conn->lastInsertId();


                        //$d = Json\Json::decode($body,Json\Json::TYPE_ARRAY);
                        $p = $data["provinces"];
                        $c = $data["categories"];
                        $q = $data["quantity"];
                        $idcustomer = $data["idcustomer"];
                        $numberOfProvinces = sizeof($p);
                        $numberOfCategories = sizeof($c);

                        $whole = floor($q / $numberOfProvinces);

                        $SQL = 'INSERT INTO redeemcodes (idorder,idprovince,redeemcode,changedby,changedat) VALUES ';

                        $SQLCategories = 'INSERT INTO order_categories (idorder,idcategory) VALUES ';
                        $SQLProvinces = 'INSERT INTO order_provinces (idorder,idprovince) VALUES ';

                        for ($i = 0; $i < $numberOfCategories; $i++) {
                            $SQLCategories .= "(" . $lastInsertID . ",'". $c[$i]['category'] .  "'),";
                        }
                        $SQLCategories = trim($SQLCategories,',');
                        $stmt = $conn->prepare($SQLCategories);
                        $stmt->execute();

                        for ($i = 0; $i < $numberOfProvinces; $i++) {
                            $SQLProvinces .= "(" . $lastInsertID . ",'". $p[$i]['province'] .  "'),";
                        }
                        $SQLProvinces = trim($SQLProvinces,',');
                        $stmt = $conn->prepare($SQLProvinces);
                        $stmt->execute();

                        $values = '';
                        $counter = 1;
                        for ($i = 0; $i < $numberOfProvinces; $i++) {
                            for ($j = 1; $j <= $whole; $j++) {
                                $values .= "(" . $lastInsertID . ",'" . $p[$i]['province'] . "','" . generateRedeemCode($idcustomer, $counter) . "'," . $loggedUser . ",SYSDATE()),";
                                $counter += 1;
                            }
                        }
                        for ($j = 1; $j <= ($q - $whole * $numberOfProvinces); $j++) {
                            $values .= "(" . $lastInsertID . ",'" . $p[0]['province'] . "','" . generateRedeemCode($idcustomer, $counter) . "'," . $loggedUser . ",SYSDATE()),";
                        }

                        $SQL .= trim($values,',');

                        //$data["sql"] = $SQLCategories;
                        //header('Content-type: application/json');
                        //echo json_encode($data);
                        //die();

                        $stmt = $conn->prepare($SQL);
                        $stmt->execute();

                        $res = array(
                            'lastInsertID'=>$lastInsertID,
                            'cate' =>$c,
                            'prov' =>$p
                        );


                        $conn->commit();
                    } catch(PDOExecption $e) {
                        $conn->rollback();
                        header('HTTP/1.0 400 Bad Request -> ' . $e->getMessage());
                    }
                    $conn = null;
                } catch (Exception $e) {
                    header('HTTP/1.0 500 Internal Server Error -> ' . $e->getMessage());
                }

                header('Content-type: application/json');
                echo json_encode($res);
            }
            else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }
        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'availablecategories':
        if ($request->isGet()) {
            if ($element) {
                try {
                    $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $SQL ="select idcategory,description from available_redeemcodes where redeemcode = '" . $element . "' ORDER BY idcategory";

                    $data = executeSelect($SQL);
                    if ($data) { // if a record is found
                        header('Content-type: application/json');
                        echo json_encode($data);
                    } else { // user is not found
                        header('HTTP/1.0 404 Not Found');
                        echo 'ENOTFOUND';
                    }
                } catch (Exception $e) {
                    header('HTTP/1.0 500 Internal Server Error');
                    echo $e->getMessage();
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "EPARAMNOTSPECIFIED";
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "EMETHODNOTALLOWED";
        }
        break;
    case 'availableorders':
        if ($request->isGet()) {
            $redeemCode = $request->getQuery()->redeemcode;
            $category = $request->getQuery()->category;


            if ($redeemCode && $category) {
                try {
                    $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $SQL = "select a.idredeemcode,d.username,d.mobile from redeemcodes a 
                      inner join order_categories b on a.idorder=b.idorder
                      inner join orders c on a.idorder=c.idorder
                      inner join customers d on c.idcustomer = d.idcustomer
                      where a.redeemcode = '" . $redeemCode . "' and a.redeemed=0
                      and b.idcategory=" . $category . " order by idredeemcode asc limit 1";

                    $data = executeSelect($SQL);
                    if ($data) { // if a record is found
                        header('Content-type: application/json');
                        echo json_encode($data);
                    } else { // user is not found
                        header('HTTP/1.0 404 Not Found');
                        echo 'ENOTFOUND';
                    }
                } catch (Exception $e) {
                    header('HTTP/1.0 500 Internal Server Error');
                    echo $e->getMessage();
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "EPARAMNOTSPECIFIED";
            }
            /*
            if ($element) {
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "EPARAMNOTSPECIFIED";
            }
            */
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "EMETHODNOTALLOWED";
        }
        break;
    case 'customerorders':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                if ($element) {

                    $SQL = "SELECT a.idorder,
                    (SELECT GROUP_CONCAT(e.description SEPARATOR ', ')
						FROM order_categories d
						INNER JOIN categories e ON d.idcategory = e.idcategory
						WHERE d.idorder=a.idorder
						GROUP BY d.idorder) as categories,
                    b.description as product,
                    (SELECT GROUP_CONCAT(g.description SEPARATOR ', ')
						FROM order_provinces f
						INNER JOIN provinces g ON f.idprovince = g.idprovince
						WHERE f.idorder=a.idorder
						GROUP BY f.idorder) as provinces,
                    a.amount,a.orderdate,
					c.description as status
                      FROM orders a
                      INNER JOIN products b ON a.idproduct = b.idproduct  
                      INNER JOIN statuses c ON a.idstatus = c.idstatus  
                      WHERE a.active = 1 AND a.idcustomer = '" . $element . "' ORDER BY a.idorder DESC";

                    $data = executeSelect($SQL);
                    header('Content-type: application/json');
                    echo json_encode($data);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Customer id not specified!";
                }
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'categories':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM categories WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idcategory = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            } elseif ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }

                $fields = "";
                $values = "";

                $SQL = "INSERT INTO " . $object . " (";
                foreach ($data as $k => $v) {
                    if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                    $fields .= $k . ",";
                    if ($v == "NULL") $values .= $v . ",";
                    else $values .= "'" . addslashes($v) . "',";
                }

                $fields .= "active,changedby,operation,changedat";
                $values .= "'1','" . $loggedUser . "','I',SYSDATE()";

                $SQL .= $fields . ") VALUES (" . $values . ")";

                $res = executeInsert($SQL);

                header('Content-type: application/json');
                echo json_encode($res);


            } elseif ($request->isPut()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                    if ($element == '') {
                        header('HTTP/1.0 400 Bad Request');
                        echo "Key value is not specified!";
                    } else {
                        $fields = "";
                        $values = "";

                        $SQL = "UPDATE " . $object . " SET ";
                        foreach ($data as $k => $v) {
                            if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                            $field = $k . " = ";
                            if ($v != "NULL") $field .= "'" . addslashes($v) . "',";
                            $SQL .= $field;
                        }

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idcategory = " . $element;

                        $res = executeUpdate($SQL, $object, 'idcategory', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE categories SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idcategory = '" . $element . "'";
                }
                $data = executeDelete($SQL);
                header('Content-type: application/json');
                echo json_encode($data);

            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'checkredeemcode':
        if ($request->isPost()) {
            $body = $request->getContent();
            if (!empty($body)) {
                $data = Json\Json::decode($body,true);
                $code = $data['code'];
                if ($code) {
                    try {
                        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql ="SELECT a.*,
                          (DATE_ADD(a.changedat, INTERVAL 30 DAY) < sysdate()) as expired 
                          FROM redeemcodes a
                          WHERE a.redeemcode = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$code]);
                        $rs = $stmt->fetch();
                        if ($rs) { // if a record is found
                            if($rs['redeemed'] == 0) { // if the code has not been redeemed yet
                                if($rs['expired'] == 0) { // if the code is not expired
                                    $res = array(
                                        'redeemcode' => $rs['redeemcode'],
                                        'idredeemcode' => $rs['idredeemcode'],
                                        'idorder' => $rs['idorder']
                                    );
                                    header('Content-type: application/json');
                                    echo json_encode($res);
                                } else { // user is not found
                                    header('HTTP/1.0 404 Not Found');
                                    echo 'EEXPIRED';
                                }
                            } else { // user is not found
                                header('HTTP/1.0 404 Not Found');
                                echo 'EALREADYREDEEMED';
                            }
                        } else { // user is not found
                            header('HTTP/1.0 404 Not Found');
                            echo 'ENOTFOUND';
                        }
                    } catch (Exception $e) {
                        header('HTTP/1.0 500 Internal Server Error');
                        echo $e->getMessage();
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo 'EINVALIDCODE';
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "EEMPTYBODY";
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "EMETHODNOTALLOWED";
        }
        break;
    case 'redeemcodes':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM redeemcodes WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idredeemcode = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            }
            elseif ($request->isPost()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "EEMPTYBODY";
                }

                $fields = "";
                $values = "";

                $SQL = "INSERT INTO " . $object . " (";
                foreach ($data as $k => $v) {
                    if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                    $fields .= $k . ",";
                    if ($v == "NULL") $values .= $v . ",";
                    else $values .= "'" . addslashes($v) . "',";
                }

                $fields .= "active,changedby,operation,changedat";
                $values .= "'1','" . $loggedUser . "','I',SYSDATE()";

                $SQL .= $fields . ") VALUES (" . $values . ")";

                $res = executeInsert($SQL);

                header('Content-type: application/json');
                echo json_encode($res);


            } elseif ($request->isPut()) {
                $body = $request->getContent();
                if (!empty($body)) {
                    $data = Json\Json::decode($body);
                    if ($element == '') {
                        header('HTTP/1.0 400 Bad Request');
                        echo "Key value is not specified!";
                    } else {
                        $fields = "";
                        $values = "";

                        $SQL = "UPDATE " . $object . " SET ";
                        foreach ($data as $k => $v) {
                            if ($k =="password") $v = password_hash($v,PASSWORD_DEFAULT);
                            $field = $k . " = ";
                            if ($v != "NULL") $field .= "'" . addslashes($v) . "',";
                            $SQL .= $field;
                        }

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idredeemcode = " . $element;

                        $res = executeUpdate($SQL, $object, 'idredeemcode', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "EEMPTYBODY";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE redeemcodes SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idredeemcode = '" . $element . "'";
                }
                $data = executeDelete($SQL);
                header('Content-type: application/json');
                echo json_encode($data);

            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "EMETHODNOTALLOWED";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'EUNAUTHORIZED';
        }
        break;
    case 'cities':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                //$SQL = "SELECT * FROM cities WHERE 1 = 1";
                $SQL = "select idcity, CONCAT(city,' (',idprovince,')') as city from cities WHERE 1=1";
                if ($element) {
                    $SQL .= " AND idcity = '" . $element . "'";
                }
                $data = executeSelect($SQL);
                header('Content-type: application/json');
                echo json_encode($data);
            } else {
                header('HTTP/1.0 405 Method Not Allowed');
                echo "Method Not Allowed!";
            }

        } else { // password is not correct
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
        }
        break;
    case 'getrandomcode':
        if ($request->isGet()) {
            $province = $request->getQuery()->province;
            if ($province) {
                try {
                    $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $SQL = "SELECT redeemcode FROM redeemcodes 
                      where redeemed=0 and idprovince='". $province . "'
                      ORDER BY idredeemcode ASC LIMIT 1;";

                    $data = executeSelect($SQL);
                    if ($data) { // if a record is found
                        header('Content-type: application/json');
                        echo json_encode($data);
                    } else { // user is not found
                        header('HTTP/1.0 404 Not Found');
                        echo 'ENOTFOUND';
                    }
                } catch (Exception $e) {
                    header('HTTP/1.0 500 Internal Server Error');
                    echo $e->getMessage();
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "EPARAMNOTSPECIFIED";
            }
            /*
            if ($element) {
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo "EPARAMNOTSPECIFIED";
            }
            */
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            echo "EMETHODNOTALLOWED";
        }
        break;

    default:
        header('HTTP/1.0 405 Method Not Allowed');
        echo "API function not available!";
}

function executeSelect($query) {
    try {
        $config = Factory::fromFile('config/settings.php', true);
        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conn = null;

        return $data;

    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo $e->getMessage();
    }
}

function executeInsert($query) {
    $res = null;
    try {
        $config = Factory::fromFile('config/settings.php', true);
        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $res = array('lastInsertID'=>$conn->lastInsertId());
            $conn->commit();
        } catch(PDOExecption $e) {
            $conn->rollback();
            header('HTTP/1.0 400 Bad Request -> ' . $e->getMessage());
        }
        $conn = null;
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error -> ' . $e->getMessage());
    }
    return $res;
}

function executeUpdate($query, $object, $keyfield, $element) {
    $res = null;

    try {
        $config = Factory::fromFile('config/settings.php', true);
        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $conn->commit();

            $SQL = "SELECT * FROM " . $object . " WHERE " . $keyfield . " = " . $element;
            $stmt = $conn->prepare($SQL);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOExecption $e) {
            $conn->rollback();
            header('HTTP/1.0 400 Bad Request');
            echo $e->getMessage();
        }
        $conn = null;
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo $e->getMessage();
    }
    return $res;
}

function executeDelete($query) {
    $res = null;
    try {
        $config = Factory::fromFile('config/settings.php', true);
        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $res = array('result'=>'DELETED');
            $conn->commit();
        } catch(PDOExecption $e) {
            $conn->rollback();
            header('HTTP/1.0 400 Bad Request -> ' . $e->getMessage());
        }
        $conn = null;
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error -> ' . $e->getMessage());
    }
    return $res;
}

/*
 * Checks for a valid authorization
 */
function checkAuthorization($req) {
    if (AUTHORIZATION_CHECK_DISABLED) return true;

    $isAuthorized = false;

    //Look for the 'authorization' header
    $authHeader = $req->getHeader('authorization');

    if ($authHeader) {
        //Extract the jwt from the Bearer
        list($jwt) = sscanf($authHeader->toString(), 'Authorization: Bearer %s');

        if ($jwt) {
            try {
                $config = Factory::fromFile('config/settings.php', true);

                //decode the jwt using the key from config
                $secretKey = base64_decode($config->get('jwt')->get('key'));
                $token = JWT::decode($jwt, $secretKey, [$config->get('jwt')->get('algorithm')]);

                $isAuthorized = true;
            } catch (Exception $e) {
                /*
                 * the token was not able to be decoded.
                 * this is likely because the signature was not able to be verified (tampered token)
                 */
                header('HTTP/1.0 401 Unauthorized');
                echo $e;
            }
        } else {
            /*
             * No token was able to be extracted from the authorization header
             */
            header('HTTP/1.0 400 Bad Request');
            echo "No token was able to be extracted from the authorization header";
        }
    } else {
        /*
         * The request lacks the authorization token
         */
        header('HTTP/1.0 400 Bad Request');
        echo 'Token not found in request';
    }
    return $isAuthorized;
}


function generateRedeemCode($customer, $index) {
    return str_pad($customer, 4, '0', STR_PAD_LEFT)
        . time() . str_pad($index, 6, '0', STR_PAD_LEFT);
}
