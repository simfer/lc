<?php
require_once('vendor/autoload.php');
// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');

/* Set locale to Italian */
setlocale(LC_ALL, 'it_IT');

//use Zend\Config\Factory;
//use Zend\Http\PhpEnvironment\Request;
//use Zend\Http\Client;
//use Zend\Json;
//use Firebase\JWT\JWT;

//$request = new Request();
//$request->setUri('http://localhost:8080/lovechallenge/server/api/v1/getrandomcode?province=AO');
//$request->setMethod('GET');

//$client = new Client();
//$response = $client->send($request);

//if ($response->isSuccess()) {
//    echo $response->getContent();
//}

// $provincesList  = readProvinces();




?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Simmaco Ferriero">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Generate Random Code</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>


    <!-- Bootstrap Core JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</head>

<body>

<div class="container">
    <div class="form-group">
        <select id="selProvince" name="selProvince" class="form-control">
        </select>
        <button id="btnGetCode" name="btnGetCode" type="button" class="btn btn-success">Genera codice casuale</button>
    </div>
    <div class="form-group">
        <input class="form-control" id="txtCode" name="txtCode" type="text" readonly>
    </div>
</div>

<script type="text/javascript">
    jQuery().ready(function() {

        //http://localhost:8080/lovechallenge/server/api/v1/provinces
        $.ajax({
            type: "GET",
            url: "http://localhost:8080/lovechallenge/server/api/v1/provinces",
            success: function (response, status) {
                var html='<select id="selProvince" name="selProvince" class="form-control">';
                $.each(response, function (i, object) {
                    html += '<option value="' + object.idprovince + '">' + object.description + '</option>';
                });
                html += '</select>';
                $("#selProvince").html(html);
            },
            error: function (response) {
                console.log(response);
            }
        });


        $("#btnGetCode").on("click", function () {
            var province = $("#selProvince").val();
            $.ajax({
                type: "GET",
                url: "http://localhost:8080/lovechallenge/server/api/v1/getrandomcode?province="+province,
                success: function (response, status) {
                    console.log(response[0]["redeemcode"]);
                    $("#txtCode").val(response[0]["redeemcode"]);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    });
</script>
</body>
</html>


