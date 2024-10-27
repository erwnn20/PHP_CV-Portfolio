<?php
session_start();

if (isset($_POST['userSelect'])) {
    $_SESSION['user']['select'] = $_POST['userSelect'];
}

header("Location: /cv");
exit;