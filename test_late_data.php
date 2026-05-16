<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Get connection
$pdo = $app['db']->getPdo();

// Check late borrows
$stmt = $pdo->prepare("
  SELECT 
    b.id, 
    u.name, 
    bo.title, 
    b.borrow_date, 
    b.due_date, 
    b.status,
    DATEDIFF(CURDATE(), DATE(b.due_date)) as hari_terlambat,
    (DATEDIFF(CURDATE(), DATE(b.due_date)) * 5000) as denda
  FROM borrows b 
  JOIN users u ON b.user_id = u.id 
  JOIN books bo ON b.book_id = bo.id 
  WHERE b.status IN ('borrowed', 'approved') 
  AND b.due_date < NOW()
  ORDER BY b.due_date ASC
");

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== DATA PEMINJAMAN TERLAMBAT ===\n";
echo "Total records: " . count($results) . "\n\n";

foreach ($results as $row) {
  echo "ID: {$row['id']} | Peminjam: {$row['name']}\n";
  echo "  Buku: {$row['title']}\n";
  echo "  Pinjam: {$row['borrow_date']} | Tempo: {$row['due_date']} | Status: {$row['status']}\n";
  echo "  Terlambat: {$row['hari_terlambat']} hari | Denda: Rp " . number_format($row['denda']) . "\n\n";
}
