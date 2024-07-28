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

// استرجاع مواد الطالب (GET)
if (preg_match('/\/api\/students\/(\d+)\/subjects/', $path, $matches) && $method == 'GET') {
    $student_id = $matches[1];
    $stmt = $pdo->prepare("SELECT subjects.* FROM subjects 
                            JOIN student_subjects ON subjects.subject_id = student_subjects.subject_id 
                            WHERE student_subjects.student_id = ?");
    $stmt->execute([$student_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($subjects);
}

// استرجاع طلاب مادة معينة (GET)
elseif (preg_match('/\/api\/subjects\/(\d+)\/students/', $path, $matches) && $method == 'GET') {
    $subject_id = $matches[1];
    $stmt = $pdo->prepare("SELECT students.* FROM students 
                            JOIN student_subjects ON students.id = student_subjects.student_id 
                            WHERE student_subjects.subject_id = ?");
    $stmt->execute([$subject_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
}

// تسجيل الطلاب في المواد (POST)
elseif ($path == '/api/student_subjects' && $method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['student_id'], $data['subject_id'])) {
        $stmt = $pdo->prepare("INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)");
        $stmt->execute([$data['student_id'], $data['subject_id']]);
        echo json_encode(['message' => 'Student registered in subject successfully']);
    } else {
        echo json_encode(['message' => 'Invalid input']);
    }
}

// استرجاع امتحانات الطالب (GET)
elseif (preg_match('/\/api\/students\/(\d+)\/exams/', $path, $matches) && $method == 'GET') {
    $student_id = $matches[1];
    $stmt = $pdo->prepare("SELECT exams.* FROM exams 
                            JOIN subjects ON exams.subject_id = subjects.subject_id 
                            JOIN student_subjects ON subjects.subject_id = student_subjects.subject_id 
                            WHERE student_subjects.student_id = ?");
    $stmt->execute([$student_id]);
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($exams);
}

// استرجاع امتحانات مادة معينة (GET)
elseif (preg_match('/\/api\/subjects\/(\d+)\/exams/', $path, $matches) && $method == 'GET') {
    $subject_id = $matches[1];
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE subject_id = ?");
    $stmt->execute([$subject_id]);
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($exams);
}

// تحديث امتحان (PUT)
elseif (preg_match('/\/api\/exams\/(\d+)/', $path, $matches) && $method == 'PUT') {
    $exam_id = $matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['subject_id'], $data['exam_date'], $data['max_score'])) {
        $stmt = $pdo->prepare("UPDATE exams SET subject_id = ?, exam_date = ?, max_score = ? WHERE exam_id = ?");
        $stmt->execute([$data['subject_id'], $data['exam_date'], $data['max_score'], $exam_id]);
        echo json_encode(['message' => 'Exam updated successfully']);
    } else {
        echo json_encode(['message' => 'Invalid input']);
    }
}

// مسار غير موجود
else {
    echo json_encode(['message' => 'Invalid endpoint']);
}
?>
