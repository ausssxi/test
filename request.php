<?php
ini_set('display_errors', 'On');
define('DB_HOST', 'localhost');
define('DB_NAME', 'gourmet');
define('DB_USER', 'root');
define('DB_PASSWORD', 'nanoninaze');

$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");

try {
     $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD, $options);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     echo $e->getMessage();
     exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$latitude = $data['y'];
$longitude = $data['x'];
$point = "POINT(${longitude} ${latitude})";



$sql = "SELECT name, x(location) AS x, y(location) AS y, altitude, ST_Distance_Sphere(location, ST_GeomFromText(:point, 4326)) d FROM shop WHERE ST_Distance_Sphere(location, ST_GeomFromText(:point, 4326)) < 1000 ORDER BY d LIMIT 10";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':point', $point, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchall(PDO::FETCH_ASSOC);
$response = json_encode($result);
header('Content-Type: application/json');
echo $response;

?>
