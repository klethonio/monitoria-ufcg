<?php

/* @author Klethônio Ferreira */
require('../dts/dbaSis.php');
sleep(1);

$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($post['list'])) {
    $list = $post['list'];
    $numExers = NULL;
    if (preg_match('/^(all)\-/i', $list)) {
        $class = explode('-', $list);
        $class = array_pop($class);
        if ($readNumExers = read('act_ordered', 'JOIN act_lists AS l ON l.id=a.list_id WHERE l.class_id=? GROUP BY a.num', [$class])) {
            foreach ($readNumExers as $numExer) {
                $numExers[] = $numExer['num'];
            }
        }
    } else {
        $readList = read('act_lists', 'WHERE id=?', [$list]);
        $list = $readList[0];
        if ($readNumExers = read('act_ordered', 'JOIN act_lists AS l ON l.id = a.list_id WHERE class_id=? AND list_id=? GROUP BY a.num', [$list['class_id'], $list['id']])) {
            foreach ($readNumExers as $numExer) {
                $numExers[] = $numExer['num'];
            }
        }
    }
    echo json_encode($numExers);
    exit();
}
if (!empty($post['orderId'])) {
    $orderId = $post['orderId'];
    if ($readOrdered = read('act_ordered', 'WHERE id=?', [$orderId])) {
        $ordered = $readOrdered[0];
        $readList = read('act_lists', 'WHERE id=?', [$ordered['list_id']]);
        $list = $readList[0];
        $data['num'] = str_pad($ordered['num'], 2, '0', STR_PAD_LEFT);
        $data['list_num'] = $list['num'];
        $data['end_date'] = date('d/m/Y', strtotime($ordered['end_date'])) . ' 23:59';
        $data['total_users'] = 0;
        $data['total_sent'] = 0;
        $data['media'] = 0;
        $data['total_corrected'] = 0;
        if ($readUsers = read('act_users', 'WHERE class_id=? AND level=0', [$list['class_id']])) {
            $data['total_users'] = count($readUsers);
        }
        if ($readSents = read('act_sent', 'WHERE order_id=?', [$ordered['id']])) {
            $data['total_sent'] = count($readSents);
            $totalGrades = 0;
            foreach ($readSents as $sent) {
                if ($sent['grade']) {
                    $totalGrades += $sent['grade'];
                    $data['total_corrected'] += 1;
                }
            }
            if ($data['total_corrected'] != 0) {
                $data['media'] = number_format($totalGrades / $data['total_corrected'], 1);
            }
        }
    }
    echo json_encode($data);
    exit();
}
if (!empty($post['listId'])) {
    $listId = $post['listId'];
    if ($readList = read('act_lists', 'WHERE id=?', [$listId])) {
        $list = $readList[0];
        $data['list_num'] = $list['num'];
        $data['total_exers'] = $list['num_exe'];
        $data['ordered'] = 0;
        $data['waited'] = 0;
        $data['total_sent'] = 0;
        $data['total_corrected'] = 0;
        $data['media'] = 0;
        if ($readOrdereds = read('act_ordered', 'WHERE list_id=?', [$listId])) {
            $data['ordered'] = count($readOrdereds);
            if ($readUsers = read('act_users', 'WHERE class_id=? AND level=0', [$list['class_id']])) {
                $totalUsers = count($readUsers);
                $data['waited'] = $data['ordered'] * $totalUsers;
                if ($readSents = read('act_sent', 'JOIN act_ordered AS o ON o.id = a.order_id WHERE o.list_id=?', [$listId])) {
                    $data['total_sent'] = count($readSents);
                    $totalGrades = 0;
                    $correcteds = 0;
                    foreach ($readSents as $sent) {
                        if ($sent['grade']) {
                            $totalGrades += $sent['grade'];
                            $correcteds += 1;
                        }
                    }
                    $data['total_corrected'] = $correcteds;
                    if ($correcteds != 0) {
                        $data['media'] = number_format($totalGrades / $correcteds, 1);
                    }
                }
            }
        }
    }
    echo json_encode($data);
    exit();
}