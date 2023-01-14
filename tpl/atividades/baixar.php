<?php

global $actUser, $setcookie, $url, $urlGet;
$sent = urlByIndex(4);
if (!($readSent = read('act_sent', 'WHERE id=?', [$sent])) || !$setcookie) {
    header('Location: ' . BASE);
} else {
    $sent = $readSent[0];
    if ($sent['user_id'] != $actUser['id'] && $actUser['level'] == 0) {
        header('Location: ' . BASE . '/' . $urlGet);
        exit();
    } else {
        header('Location: ' . BASE . '/baixar-m.php?user=' . $actUser['id'] . '&sent=' . $sent['id']);
    }
}