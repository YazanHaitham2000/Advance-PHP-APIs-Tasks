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

// المعلمين
if (preg_match('/^\/api\/teachers\/?/', $path)) {
    if ($method == 'POST' && $path == '/api/teachers') {
        // إضافة معلم
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'], $data['subject_id'], $data['contact_info'])) {
            $stmt = $pdo->prepare("INSERT INTO teachers (name, subject_id, contact_info) VALUES (?, ?, ?)");
            $stmt->execute([$data['name'], $data['subject_id'], $data['contact_info']]);
            echo json_encode(['message' => 'Teacher created successfully', 'teacher_id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['message' => 'Invalid input']);
        }
    } elseif ($method == 'GET' && $path == '/api/teachers') {
        // الحصول على جميع المعلمين
        $stmt = $pdo->query("SELECT * FROM teachers");
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($teachers);
    } elseif (preg_match('/\/api\/teachers\/(\d+)/', $path, $matches) && $method == 'GET') {
        // الحصول على معلم بواسطة ID
        $teacher_id = $matches[1];
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($teacher ? $teacher : ['message' => 'Teacher not found']);
    }
}

// المواد
elseif (preg_match('/^\/api\/subjects\/?/', $path)) {
    if ($method == 'POST' && $path == '/api/subjects') {
        // إضافة مادة
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'], $data['description'])) {
            $stmt = $pdo->prepare("INSERT INTO subjects (name, description) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['description']]);
            echo json_encode(['message' => 'Subject created successfully', 'subject_id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['message' => 'Invalid input']);
        }
    } elseif ($method == 'GET' && $path == '/api/subjects') {
        // الحصول على جميع المواد
        $stmt = $pdo->query("SELECT * FROM subjects");
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($subjects);
    } elseif (preg_match('/\/api\/subjects\/(\d+)/', $path, $matches) && $method == 'GET') {
        // الحصول على مادة بواسطة ID
        $subject_id = $matches[1];
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_id = ?");
        $stmt->execute([$subject_id]);
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($subject ? $subject : ['message' => 'Subject not found']);
    }
}

// الامتحانات
elseif (preg_match('/^\/api\/exams\/?/', $path)) {
    if ($method == 'POST' && $path == '/api/exams') {
        // إضافة امتحان
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['subject_id'], $data['exam_date'], $data['max_score'])) {
            $stmt = $pdo->prepare("INSERT INTO exams (subject_id, exam_date, max_score) VALUES (?, ?, ?)");
            $stmt->execute([$data['subject_id'], $data['exam_date'], $data['max_score']]);
            echo json_encode(['message' => 'Exam created successfully', 'exam_id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['message' => 'Invalid input']);
        }
    } elseif ($method == 'GET' && $path == '/api/exams') {
        // الحصول على جميع الامتحانات
        $stmt = $pdo->query("SELECT * FROM exams");
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($exams);
    } elseif (preg_match('/\/api\/exams\/(\d+)/', $path, $matches) && $method == 'GET') {
        // الحصول على امتحان بواسطة ID
        $exam_id = $matches[1];
        $stmt = $pdo->prepare("SELECT * FROM exams WHERE exam_id = ?");
        $stmt->execute([$exam_id]);
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($exam ? $exam : ['message' => 'Exam not found']);
    }
} else {
    // مسار غير موجود
    echo json_encode(['message' => 'Invalid endpoint']);
}
?>
