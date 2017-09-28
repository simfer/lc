<?php
include "config.php";

require_once('vendor/autoload.php');

use Zend\Config\Factory;
use Zend\Http\PhpEnvironment\Request;
use Zend\Json;
use Firebase\JWT\JWT;

// this is to be removed and adjusted
$loggedUser = 1;

// get a new request
$request = new Request();

$request_parts = explode('?', $request->getUriString());

$request_parts = explode('&', $request_parts[1]);

$customerUrl = (isset($request_parts[0]) ? $request_parts[0] : '');
$tokenUrl = (isset($request_parts[1]) ? $request_parts[1] : ''); 

$customerId = str_replace('idcust=', '', $customerUrl);
$tokenId = str_replace('tokenId=', '', $tokenUrl);


$SQL = "SELECT * FROM customers WHERE idcustomer = " . $customerId; 


try {
	$config = Factory::fromFile('config/settings.php', true);
    $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

    $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $conn->prepare($SQL);
  	$stmt->execute();
	$data = $stmt->fetch();


    if ($data) {
    	if($data["registrationtoken"] == null && $data["active"] == 1 && $data["registered"] == 1) {
    		echo "Utente già registrato!";
    	} else {
    		$customerId = $data["idcustomer"];
    		$SQLREGISTER = "UPDATE customers SET active = 1, registrationtoken = null, registered = 1 WHERE idcustomer = '" . $customerId . "'";
    		$conn->beginTransaction();
        	$stmt = $conn->prepare($SQLREGISTER);
        	$stmt->execute();
        	$conn->commit();
			
			echo 'Registratione confermata! Fai click <a href="http://localhost:4200/login">qui</a> per accedere con le tue credenziali!';

        	//$SQL = "SELECT * FROM customers WHERE active = 1 AND idcustomer = '" . $customerId ."'";
        	//$stmt = $conn->prepare($SQL);
        	//$stmt->execute();
        	//$data = $stmt->fetch();
    	}
     } else {
     	echo "Utente non trovato! E' necessario registrarsi nuovamente.";
     }

    $conn = null;


        } catch (Exception $e) {
          echo $e->getMessage();
        }



?>

