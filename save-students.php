<?php
header('Content-Type: application/json');

try {
    $body = file_get_contents('php://input');
    $request = json_decode($body, true);

    $pdo = new PDO('mysql:host=localhost;dbname=sekolah', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $query = $pdo->prepare("INSERT INTO students (nik, nama) VALUES (:nik, :name)");
    $query->bindValue(':nik', $request['nik'], PDO::PARAM_STR);
    $query->bindValue(':name', $request['name'], PDO::PARAM_STR);
    $query->execute();

    $id = $pdo->lastInsertId();

    // savestudentsdata
    $query = $pdo->prepare("SELECT * FROM students WHERE id = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $student = $query->fetch();

    echo json_encode([
        'status' => true,
        'student' => $student
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'error' => 'Failed to save student: ' . $e->getMessage()
    ]);
}
?>
