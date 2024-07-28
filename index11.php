<?php
// إعداد الاتصال بقاعدة البيانات
$host = 'localhost';
$db = 'school_management';
$user = 'dbyazan';
$pass = '0000';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

// الحصول على طريقة الطلب
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

// مسارات API
if ($path == '/students' && $method == 'POST') {
    // إنشاء سجل طالب جديد
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['name'], $data['date_of_birth'], $data['address'], $data['contact_info'])) {
        $stmt = $pdo->prepare("INSERT INTO students (name, date_of_birth, address, contact_info) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['date_of_birth'], $data['address'], $data['contact_info']]);
        echo json_encode(['message' => 'Student created successfully']);
    } else {
        echo json_encode(['message' => 'Invalid input']);
    }
} elseif ($path == '/students' && $method == 'GET') {
    // استرجاع جميع سجلات الطلاب
    $stmt = $pdo->query("SELECT * FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
} elseif (preg_match('/\/students\/(\d+)/', $path, $matches) && $method == 'GET') {
    // استرجاع سجل طالب فردي بواسطة المعرف
    $id = $matches[1];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($student ? $student : ['message' => 'Student not found']);
} elseif (preg_match('/\/students\/(\d+)/', $path, $matches) && $method == 'PUT') {
    // تحديث سجل طالب موجود
    $id = $matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['name'], $data['date_of_birth'], $data['address'], $data['contact_info'])) {
        $stmt = $pdo->prepare("UPDATE students SET name = ?, date_of_birth = ?, address = ?, contact_info = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['date_of_birth'], $data['address'], $data['contact_info'], $id]);
        echo json_encode(['message' => 'Student updated successfully']);
    } else {
        echo json_encode(['message' => 'Invalid input']);
    }
} elseif (preg_match('/\/students\/(\d+)/', $path, $matches) && $method == 'DELETE') {
    // حذف سجل طالب
    $id = $matches[1];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['message' => 'Student deleted successfully']);
} else {
    // مسار غير موجود
    echo json_encode(['message' => 'Invalid endpoint']);
}
?>
