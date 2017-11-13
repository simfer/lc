<?php
require_once('vendor/autoload.php');

use Zend\Config\Factory;
use Zend\Http\PhpEnvironment\Request;
use Zend\Json;
use Firebase\JWT\JWT;

$t = 0;

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');

/* Set locale to Italian */
setlocale(LC_ALL, 'it_IT');


//echo date('Y-m-d', strtotime($offset));

$numCustomers = 10;
$numOrders = 100;



$provincesList  = readProvinces();
//var_dump($provincesList);
//die();

/*
 * CUSTOMERS
 */
truncateTable('customers');

$customers = 'INSERT INTO customers (lastname,firstname,username,password,registered,subscribed,gender,
    dateofbirth,placeofbirth,mobile,email,active,changedby,operation,changedat) VALUES ';

for ($i = 1; $i <= $numCustomers; $i++) {
    $yearOffset = "-" . rand(20,60) . " year";
    $dayOffset = " +" . rand(1,365) . " day";
    $lastname = 'LN' . str_pad($i, 8, "0", STR_PAD_LEFT);
    $firstname = 'FN' . str_pad($i, 8, "0", STR_PAD_LEFT);
    $username = 'UN' . str_pad($i, 8, "0", STR_PAD_LEFT);
    $password = '$2y$10$mqESKvu1dSE51o5pn1zcF.08nokHTALb/b0H5nc/YX.nuNIh8Ef2K';
    $registered = 1;
    $subscribed = 1;
    $gender = (rand(1, 2)==1?'M':'F');
    $dateofbirth = date('Y-m-d', strtotime($yearOffset));
    $placeofbirth = 'POB' . str_pad($i, 8, "0", STR_PAD_LEFT);
    $mobile = rand(333,399).str_pad(rand(1,9999999), 7, "0", STR_PAD_LEFT);
    $email = 'careter33@gustr.com';

    $customer = array(
            'lastname' => $lastname,
            'firstname' => $firstname,
            'username' => $username,
            'password' => $password,
            'registered' => $registered,
            'subscribed' => $subscribed,
            'gender' => $gender,
            'dateofbirth' => $dateofbirth,
            'placeofbirth' => $placeofbirth,
            'mobile' => $mobile,
            'email' => $email
    );
    $customers .= GeneraQuery($customer);
}

$customers = trim($customers,',');
//echo $customers;

BulkInsert($customers);


/*
 * ORDERS
 */





truncateTable('orders');
truncateTable('redeemcodes');
truncateTable('order_categories');
truncateTable('order_provinces');

$orders = 'INSERT INTO orders (idcustomer,idproduct,quantity,amount,orderdate,idstatus,active,changedby,operation,changedat) VALUES ';
$redeemCodes = 'INSERT INTO redeemcodes (idorder,idprovince,redeemcode,active,changedby,operation,changedat) VALUES ';
$order_categories = 'INSERT INTO order_categories (idorder,idcategory) VALUES';
$order_provinces = 'INSERT INTO order_provinces (idorder,idprovince) VALUES';

$productPrices = array(1=>'5',2=>'10',3=>'20');
$productQuantities = array(1=>'10',2=>'20',3=>'50');

$counter = 1;
for ($i = 1; $i <= $numOrders; $i++) {
    $offset = "+" . rand(1,5) . " day";
    $idcustomer = rand(1, $numCustomers);
    $idproduct = rand(1, 3);
    $categories = generateRandomCategories();
    $provinces = $provincesList[rand(0, 102)][0] . (rand(0,1)==0 ? ('|' . $provincesList[rand(0, 102)][0]) : '');
    $quantity = $productQuantities[$idproduct];
    $amount = $productPrices[$idproduct];
    $orderdate = date('Y-m-d H:i:s', strtotime($offset));
    $idstatus = rand(1, 3);

    $order = array(
        'idcustomer' => $idcustomer,
        'idproduct' => $idproduct,
        'quantity' => $quantity,
        'amount' => $amount,
        'orderdate' => $orderdate,
        'idstatus' => $idstatus
    );
    $orders .= GeneraQuery($order);

    // generate order_categories
    $arrayCategories = explode('|',$categories);
    $numCategories = count($arrayCategories);
    for ($r = 0; $r < $numCategories; $r++) {
        $order_categories .= '(' . $i . ',' . $arrayCategories[$r] . '),';
    }

    // generate order_provinces
    $arrayProvinces = explode('|',$provinces);
    $numProvinces = count($arrayProvinces);
    for ($r = 0; $r < $numProvinces; $r++) {
        $order_provinces .= "(" . $i . ",'" . $arrayProvinces[$r] . "'),";
    }

    // generate redeem codes
    $q = $quantity / $numProvinces;


    for ($r = 0; $r < $numProvinces; $r++) {
        for ($j = 1; $j <= $q; $j++) {
            $idorder = $i;
            $idprovince = $arrayProvinces[$r];
            $redeemCode = generateRedeemCode($idorder,$idprovince);
            $rCode =  array(
                'idorder' => $idorder,
                'idprovince' => $idprovince,
                'redeemCode' => $redeemCode
            );
            $redeemCodes .= GeneraQuery($rCode);
            $counter += 1;
        }
    }
}

$orders = trim($orders,',');
//echo $orders;
BulkInsert($orders);

$redeemCodes = trim($redeemCodes,',');
//echo $redeemCodes;
BulkInsert($redeemCodes);

$order_categories = trim($order_categories,',');
//echo $order_categories;
BulkInsert($order_categories);

$order_provinces = trim($order_provinces,',');
echo $order_provinces;
BulkInsert($order_provinces);

function readProvinces() {
    try {
        $config = Factory::fromFile('config/settings.php', true);
        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {

            $sql = "select * from provinces";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            return $data;

        } catch (PDOException $e) {
            return $e->getMessage();
        }
        $conn = null;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function truncateTable($tableName) {
    $fullSql = "";

    try {
        $config = Factory::fromFile('config/settings.php', true);
        $dsn = 'mysql:host=' . $config->get('database')->get('host') . ';dbname=' . $config->get('database')->get('name');

        $conn = new PDO($dsn, $config->get('database')->get('user'), $config->get('database')->get('password'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $conn->beginTransaction();

            $sql = "truncate table " . $tableName;
            $fullSql .= $sql;
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $conn->commit();

            header('Content-type: application/json');
            echo json_encode(array(
                'status' => 'success',
                'result' => 'OK',
                'sql' => $fullSql
            ));

        } catch (PDOException $e) {

            $conn->rollBack();

            header('HTTP/1.0 500 Internal Server Error');
            echo $e->getMessage();
        }

        $conn = null;
    } catch (PDOException $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo $e->getMessage();
    }
}

function BulkInsert($query) {
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

        } catch (PDOException $e) {
            $conn->rollBack();
        }

        $conn = null;
    } catch (PDOException $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo $e->getMessage();
    }
}

function GeneraQuery($record)
{
    $values = "";

    foreach ($record as $key => $value) {
        //$$key = $record[$key];
        //$columns .= $key . ",";
        $values .= "'" . addslashes($value) . "',";
    }

    $values = $values . "1,1,'I',sysdate()";
    $sql = " (" . $values . "),";

    return $sql;
}

function generateRedeemCode($order, $province) {
    return time() . getProvinceNumberCode($province);
}

/* function generateRedeemCode($order, $province) {
    return str_pad($order, 4, '0', STR_PAD_LEFT)
        . time() . getProvinceNumberCode($province);
} */

function FormatDBDate($dateToFormat)
{
    $ddd = "null";
    if (!empty($dateToFormat)) {
        list ($giorno, $mese, $anno) = split('[/.-]', $dateToFormat);
        $ddd = "'" . $anno . "-" . $mese . "-" . $giorno . "'";
    }
    return $ddd;
}

function generateRandomCategories() {
    $p = rand(1,7);
    $r = (($p & 4)>0?'1|':'') . (($p & 2)>0?'2|':'') . (($p & 1)>0?'3':'');

    return trim($r,'|');
}
function getProvinceNumberCode($c) {
    return str_pad(ord($c[0])-64, 2, '0', STR_PAD_LEFT)
        . str_pad(ord($c[1])-64, 2, '0', STR_PAD_LEFT);
}
?>