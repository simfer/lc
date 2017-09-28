<?php
include "config.php";

define('AUTHORIZATION_CHECK_DISABLED', true);

require_once('vendor/autoload.php');

use Zend\Config\Factory;
use Zend\Http\PhpEnvironment\Request;
use Zend\Json;
use Firebase\JWT\JWT;

// this is to be removed and adjusted
$loggedUser = 1;

// get a new request
$request = new Request();

$request_parts = explode('/', $request->getQuery("url"));
$object = (isset($request_parts[0]) ? $request_parts[0] : ''); //the first piece is the object
$element = (isset($request_parts[1]) ? $request_parts[1] : ''); // the second is the object's element on which we want to perform the operation


switch(strtolower($object)) {
    case 'userlogin':
        if ($request->isPost()) {
            $body = $request->getContent();
            if (!empty($body)) {
                $data = Json\Json::decode($body,true);
                $username = $data['username'];
                $password = $data['password'];
                if ($username && $password) {
                    try {
                        $config = Factory::fromFile('config/settings.php', true);
                        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

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
                                $expire     = $notBefore + 604800; // Adding 60 seconds
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
                        $config = Factory::fromFile('config/settings.php', true);
                        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

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
                $registrationtoken = base64_encode(random_bytes(32));
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
                    $config = Factory::fromFile('config/settings.php', true);
                    $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

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
                        $message = 'Fai click <a href="http://localhost:8080/lovechallenge/server/confirmregistration.php?idcust=' . $idcustomer . '&tokenId=' . $registrationtoken . '">qui</a> per confermare la tua registrazione!';
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
                        $conn = new PDO (DB_DSN, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
        $to = 'careter33@gustr.com';
        $subject = 'Love Challenge - Conferma registrazione!';
        $message = 'Conferma la tua registrazione!';
        $headers = 'From: webmaster@example.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
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
                            $config = Factory::fromFile('config/settings.php', true);
                            $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

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
    case 'regions':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM regions WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idregion = '" . $element . "'";
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

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idregion = " . $element;

                        $res = executeUpdate($SQL, $object, 'idregion', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE regions SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idregion = '" . $element . "'";
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
    case 'orders':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM orders WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idorder = '" . $element . "'";
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

                $res = null;
                try {
                    $conn = new PDO (DB_DSN, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    try {
                        $conn->beginTransaction();
                        $stmt = $conn->prepare($SQL);
                        $stmt->execute();
                        $lastInsertID = $conn->lastInsertId();


                        $d = Json\Json::decode($body,Json\Json::TYPE_ARRAY);
                        $r = $d["regions"];
                        $q = $d["quantity"];
                        $idcustomer = $d["idcustomer"];
                        $regions = explode('|', $r);
                        $numberOfRegions = sizeof($regions);
                        $whole = floor($q / $numberOfRegions);

                        $SQL = 'INSERT INTO redeemcodes (idorder,idregion,redeemcode,changedby,changedat) VALUES ';

                        $values = '';
                        $counter = 1;
                        for ($i = 0; $i < $numberOfRegions; $i++) {
                            for ($j = 1; $j <= $whole; $j++) {
                                $values .= "(" . $lastInsertID . "," . $regions[$i] . ",'" . generateRedeemCode($idcustomer, $counter) . "'," . $loggedUser . ",SYSDATE()),";
                                $counter += 1;
                            }
                        }
                        for ($j = 1; $j <= ($q - $whole * $numberOfRegions); $j++) {
                            $values .= "(" . $lastInsertID . "," . $regions[0] . ",'" . generateRedeemCode($idcustomer, $counter) . "'," . $loggedUser . ",SYSDATE()),";
                        }

                        $SQL .= trim($values,',');

                        $stmt = $conn->prepare($SQL);
                        $stmt->execute();

                        $res = array('lastInsertID'=>$lastInsertID);


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

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idorder = " . $element;

                        $res = executeUpdate($SQL, $object, 'idorder', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE orders SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idorder = '" . $element . "'";
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
    case 'colors':
        if (checkAuthorization($request)) { // if the request is valid
            if ($request->isGet()) {
                $SQL = "SELECT * FROM colors WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idcolor = '" . $element . "'";
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

                        $SQL .= "changedby=" . $loggedUser . ",operation='U',changedat=SYSDATE() WHERE idcolor = " . $element;

                        $res = executeUpdate($SQL, $object, 'idcolor', $element);

                        header('Content-type: application/json');
                        echo json_encode($res);

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo "Request body is empty!";
                }
            } elseif ($request->isDelete()) {
                $SQL = "UPDATE colors SET active = 0,changedby=" . $loggedUser . ",operation='D',changedat=SYSDATE() WHERE active = 1";
                if ($element) {
                    $SQL .= " AND idcolor = '" . $element . "'";
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

    default:

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
        $conn = new PDO (DB_DSN, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
        $conn = new PDO (DB_DSN, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
        $conn = new PDO (DB_DSN, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
    return str_pad($customer, 4, '0', STR_PAD_LEFT) . '-' . time() . str_pad($index, 2, '0', STR_PAD_LEFT); //date("Ymd");
}