<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=sekolah', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Ambil parameter halaman dan jumlah data per halaman
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 5;
    $offset = ($page - 1) * $perPage;

    // Query untuk mengambil data dengan pagination
    $query = $pdo->prepare("SELECT * FROM students LIMIT :limit OFFSET :offset");
    $query->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    $students = $query->fetchAll();

    // Query untuk menghitung total data
    $countQuery = $pdo->prepare("SELECT COUNT(*) FROM students");
    $countQuery->execute();
    $total = $countQuery->fetchColumn();

    $totalPages = ceil($total / $perPage);

    echo json_encode([
        'status' => true,
        'students' => $students,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
}
?>
