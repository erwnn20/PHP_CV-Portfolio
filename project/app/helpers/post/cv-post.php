<?php

require_once 'app/models/cv-data.php';
require_once 'app/models/img-data.php';
require_once 'app/helpers/uuid.php';

session_start();

function appendData(string $dbDataID, array|string $newData): void
{
    $data = json_decode(CV::getData($_SESSION['user']['id'])[$dbDataID] ?? '[]', true);
    $data[] = $newData;

    CV::update($dbDataID, $_SESSION['user']['id'], $data);
}

function deleteData(string $dbDataID, int $delIndex): void
{
    $data = array();
    foreach (json_decode(CV::getData($_SESSION['user']['id'])[$dbDataID] ?? '[]', true) as $i => $element) {
        if ($i != $delIndex)
            $data[] = $element;
    }

    CV::update($dbDataID, $_SESSION['user']['id'], $data);
}

// global infos
if (isset($_POST['cvTitle'])) CV::update('title', $_SESSION['user']['id'], $_POST['cvTitle']);
if (isset($_POST['cvDescription'])) CV::update('description', $_SESSION['user']['id'], $_POST['cvDescription']);
if (isset($_POST['cvEmail'])) CV::update('email', $_SESSION['user']['id'], $_POST['cvEmail']);
if (isset($_POST['cvPhone'])) CV::update('phone_number', $_SESSION['user']['id'], $_POST['cvPhone']);
if (isset($_POST['cvAddress'])) CV::update('address', $_SESSION['user']['id'], $_POST['cvAddress']);

if (isset($_POST['style_background']))
    CV::updateStyle(
        cvID: CV::getData($_SESSION['user']['id'])['id'],
        style: array(
            'background' => $_POST['style_background'],
            'text_color' => $_POST['style_text'],
            'background_2' => $_POST['style_background_2'],
            'text_color_2' => $_POST['style_text_2']
        ));

if (Images::save('cv/', 'cvProfileImage', CV::getData($_SESSION['user']['id'] ?? 0)['id']))
    CV::setImg(true, CV::getData($_SESSION['user']['id'] ?? 0)['id']);

if (isset($_POST['delCvProfileImage'])) {
    CV::setImg(false, CV::getData($_SESSION['user']['id'] ?? 0)['id']);
    Images::delete('cv/', $_POST['delCvProfileImage']);
}

// skills, languages, interests infos
if (isset($_POST['newSkill']))
    appendData(
        'skills',
        array(
            'skill' => $_POST['newSkill'],
            'year_exp' => $_POST['skillExp'],
        ));
if (isset($_POST['delSkillIndex']))
    deleteData('skills', $_POST['delSkillIndex']);

if (isset($_POST['languageName']))
    appendData(
        'languages',
        array(
            'lang' => $_POST['languageName'],
            'level' => $_POST['languageLevel'],
        ));
if (isset($_POST['delLangIndex']))
    deleteData('languages', $_POST['delLangIndex']);

if (isset($_POST['interestName']))
    appendData(
        'interests',
        $_POST['interestName']
    );
if (isset($_POST['delInterestIndex']))
    deleteData('interests', $_POST['delInterestIndex']);

// experience, certificate
if (isset($_POST['experienceTitle']))
    appendData(
        'experiences',
        array(
            'role' => $_POST['experienceTitle'],
            'company' => $_POST['experienceCompany'],
            'tasks' => $_POST['task'],
            'start_date' => $_POST['experienceStartDate'],
            'end_date' => $_POST['experienceEndDate'] ?? ''
        ));
if (isset($_POST['delExpIndex']))
    deleteData('experiences', $_POST['delExpIndex']);

if (isset($_POST['certificateTitle']))
    appendData(
        'certificates',
        array(
            'degree' => $_POST['certificateTitle'],
            'school' => $_POST['certificateSchool'],
            'date' => $_POST['certificateYear'],
        ));
if (isset($_POST['delCertificateIndex']))
    deleteData('certificates', $_POST['delCertificateIndex']);

header("Location: /cv/edit");
exit;