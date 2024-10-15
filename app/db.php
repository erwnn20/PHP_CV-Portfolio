<?php
// Database connection using PDO
$host = 'db';
$db = 'cv_db';
$user = 'root'; // Update this with your MySQL username
$pass = 'root';     // Update this with your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Generate uuidV4
function uuid_v4(): string
{
    do {
        try {
            $data = random_bytes(16);
        } catch (Exception $e) {
            $data = '';
        }
    } while (strlen($data) < 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Fetch user information from the database
function getUserInfo($id)
{
    global $pdo;
    if ($id) {
        $stmt = $pdo->prepare('SELECT email, first_name, last_name, admin FROM user WHERE id = :id');
        $stmt->execute(array('id' => $id));
        return $stmt->fetch();
    }
    return array(
        'email' => '',
        'first_name' => '',
        'last_name' => '',
        'admin' => false
    );
}