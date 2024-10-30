<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']);
    $sql = "SELECT * FROM productos WHERE nombre_producto LIKE :query OR descripcion_producto LIKE :query";
    $stmt = $base->prepare($sql);
    $stmt->execute([':query' => '%' . $query . '%']);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);
}
?>
