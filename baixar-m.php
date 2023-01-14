<?php

/* @author Klethônio Ferreira */
ob_start();
session_start();
include './dts/dbaSis.php';
include './dts/getSis.php';

$actUser = getUser($_GET['user']);

$readSent = read('act_sent', 'WHERE id=?', [$_GET['sent']]);
$sent = $readSent[0];

if ($sent['user_id'] != $actUser['id'] && $actUser['level'] == 0) {
    header('Location: ' . BASE);
    exit();
}

$readClass = read('act_config_class', 'WHERE id=?', [$actUser['class_id']]);
$class = $readClass[0];
$discipline = getDiscipline($class['discipline_id'], 'url');
$filename = './enviados/' . $class['period'] . '/' . $discipline . '-' . $class['class'] . '/' . $sent['file'];


if (!file_exists($filename)) {
    $_SESSION['error'] = 'Erro: Não foi possível efetuar o download!';
    if ($actUser['level'] == 0) {
        header('Location: ' . BASE . '/' . $class['period'] . '/' . $discipline . '-' . $class['class'] . '/atividades/minhas-atividades');
    } else {
        header('Location: ' . BASE . '/' . $class['period'] . '/' . $discipline . '-' . $class['class'] . '/atividades/corrigir-atividades');
    }
    exit();
} else {
    switch (strtolower(substr(strrchr(basename($filename), "."), 1))) {
        case "pdf": $type = "application/pdf";
            break;
        case "xlsx": $type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            break;
        case "xls": $type = "application/vnd.ms-excel";
            break;
        case "ods": $type = "application/vnd.oasis.opendocument.spreadsheet";
            break;
        default : $type = "application/octet-stream";
    }
    header('Content-Description: File Transfer');
    header("Content-Type: " . $type);
    header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Length: ' . filesize($filename)); //Remove

    ob_clean();
    flush();

    readfile($filename);
    exit();
}