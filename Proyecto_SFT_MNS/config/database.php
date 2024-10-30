<?php
try{
    $base = new PDO('mysql: host= localhost; dbname=proyecto_shopping_footwear_tenis_mns', 'root', '123456');
    $base -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $base -> exec("SET CHARACTER SET UTF8");
}catch(exception $e){
    die('Error'. $e-> getMessage());
    echo "Linea error". $e -> getLine();
}
?>