<?php
session_start();
require_once 'util/user.php';
require_once 'util/cv.php';
global $pdo;

function appendData(string $dbDataID, array|string $newData): void
{
    global $pdo;
    $data = json_decode(CV::getData($_SESSION['user']['id'])[$dbDataID] ?? '[]', true);
    $data[] = $newData;

    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, '.$dbDataID.') VALUES (:id, :creator_id, :'.$dbDataID.') ON DUPLICATE KEY UPDATE '.$dbDataID.' = VALUES('.$dbDataID.')');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        $dbDataID => json_encode($data)
    ));
}

function deleteData(string $dbDataID, int $delIndex): void
{
    global $pdo;
    $data = array();
    foreach (json_decode(CV::getData($_SESSION['user']['id'])[$dbDataID] ?? '[]', true) as $i => $element) {
        if ($i != $delIndex)
            $data[] = $element;
    }

    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, '.$dbDataID.') VALUES (:id, :creator_id, :'.$dbDataID.') ON DUPLICATE KEY UPDATE '.$dbDataID.' = VALUES('.$dbDataID.')');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        $dbDataID => json_encode($data)
    ));
}

// global infos
if (isset($_POST['cvTitle'])) {
    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, title) VALUES (:id, :creator_id, :title) ON DUPLICATE KEY UPDATE title = VALUES(title)');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        'title' => $_POST['cvTitle']
    ));
}
if (isset($_POST['cvDescription'])) {
    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, description) VALUES (:id, :creator_id, :description) ON DUPLICATE KEY UPDATE description = VALUES(description)');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        'description' => $_POST['cvDescription']
    ));
}
if (isset($_POST['cvEmail'])) {
    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, email) VALUES (:id, :creator_id, :email) ON DUPLICATE KEY UPDATE email = VALUES(email)');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        'email' => $_POST['cvEmail']
    ));
}
if (isset($_POST['cvPhone'])) {
    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, phone_number) VALUES (:id, :creator_id, :phone_number) ON DUPLICATE KEY UPDATE phone_number = VALUES(phone_number)');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        'phone_number' => $_POST['cvPhone']
    ));
}
if (isset($_POST['cvAddress'])) {
    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, address) VALUES (:id, :creator_id, :address) ON DUPLICATE KEY UPDATE address = VALUES(address)');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user']['id'],
        'address' => $_POST['cvAddress']
    ));
}

if (User::saveImg('img/cv/', 'cvProfileImage', CV::getData($_SESSION['user']['id'] ?? 0)['id'])) {
    $stmt = $pdo->prepare('UPDATE cv SET image = TRUE WHERE id = :id;');
    $stmt->execute(array(
        'id' => CV::getData($_SESSION['user']['id'] ?? 0)['id']
    ));
}
if (isset($_POST['delCvProfileImage'])) {
    $stmt = $pdo->prepare('UPDATE cv SET image = FALSE WHERE id = :id;');
    $stmt->execute(array(
        'id' => $_POST['delCvProfileImage']
    ));
    User::deleteImg('img/cv/', $_POST['delCvProfileImage']);
}

// skills, languages, interests infos
if (isset($_POST['newSkill'])) {
    appendData('skills', array(
        'skill' => $_POST['newSkill'],
        'year_exp' => $_POST['skillExp'],
    ));
}
if (isset($_POST['delSkillIndex'])) {
    deleteData('skills', $_POST['delSkillIndex']);
}

if (isset($_POST['languageName'])) {
    appendData('languages', array(
        'lang' => $_POST['languageName'],
        'level' => $_POST['languageLevel'],
    ));
}
if (isset($_POST['delLangIndex'])) {
    deleteData('languages', $_POST['delLangIndex']);
}

if (isset($_POST['interestName'])) {
    appendData('interests', $_POST['interestName']);
}
if (isset($_POST['delInterestIndex'])) {
    deleteData('interests', $_POST['delInterestIndex']);
}

// experience, certificate
if (isset($_POST['experienceTitle'])) {
    appendData('experiences', array(
        'role' => $_POST['experienceTitle'],
        'company' => $_POST['experienceCompany'],
        'tasks' => $_POST['task'],
        'start_date' => $_POST['experienceStartDate'],
        'end_date' => $_POST['experienceEndDate'] ?? ''
    ));
}
if (isset($_POST['delExpIndex'])) {
    deleteData('experiences', $_POST['delExpIndex']);
}

if (isset($_POST['certificateTitle'])) {
    appendData('certificates', array(
        'degree' => $_POST['certificateTitle'],
        'school' => $_POST['certificateSchool'],
        'date' => $_POST['certificateYear'],
    ));
}
if (isset($_POST['delCertificateIndex'])) {
    deleteData('certificates', $_POST['delCertificateIndex']);
}

header("Location: /cv/edit");
exit;