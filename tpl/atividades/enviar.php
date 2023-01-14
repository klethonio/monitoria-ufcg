<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class, $period;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 0, TRUE) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um aluno!', 'alert');
} else {
    ?>
    <div id="activities">
        <h1>Atividades Requisitadas</h1>
        <?php
        if (!empty($_SESSION['success'])) {
            msg($_SESSION['success'], 'success');
            unset($_SESSION['success']);
        }
        if (!empty($_FILES['fileMatlab'])) {

            foreach ($_FILES['fileMatlab']['tmp_name'] as $key => $file) {
                if ($file) {
                    $fileMatlab['name'] = $_FILES['fileMatlab']['name'][$key];
                    $fileMatlab['tmp_name'] = $_FILES['fileMatlab']['tmp_name'][$key];
                    $fileMatlab['size'] = $_FILES['fileMatlab']['size'][$key];
                    $fileMatlab['order_id'] = $key;
                    break;
                }
            }
            $list = getList(getExer($fileMatlab['order_id'], 'list_id'));
            $language = getLanguage($list['type']);
            if (!$fileMatlab['order_id'] || read('act_ordered', 'WHERE id=? AND end_date<NOW()', [$fileMatlab['order_id']])) {
                header('Location: ' . BASE . '/' . $urlGet . '/atividades/enviar');
            } elseif (!preg_match('/\.(' . $language['extension'] . ')$/i', $fileMatlab['name'], $ext)) {
                msg('Error: A extensão do arquivo pode ser: .' . preg_replace('/[|]{1}/', ', .', $language['extension']), 'error');
            } elseif ($fileMatlab['size'] > (5 * 1024 * 1024)) {
                msg('Erro: O tamanho do arquivo não deve ultrapassar 5MB!', 'error');
            } else {
                $order = getExer($fileMatlab['order_id']);
                $userFullName = explode(' ', trim($actUser['name']));
                $userName = $userFullName[0] . end($userFullName);
                $fileMatlab['name'] = 'L' . getList($order['list_id'], 'num') . 'Ex' . str_pad($order['num'], 2, '0', STR_PAD_LEFT) . $userName . '_' . $actUser['id'] . $ext[0];
                if (!file_exists('./enviados')) {
                    mkdir('./enviados', 0777);
                }
                if (!file_exists('./enviados/' . $period)) {
                    mkdir('./enviados/' . $period, 0777);
                }
                if (!file_exists('./enviados/' . $urlGet)) {
                    mkdir('./enviados/' . $urlGet, 0777);
                }
                if (!file_exists('./enviados/' . $urlGet . '/' . $fileMatlab['name'])) {
                    unlink('./enviados/' . $urlGet . '/' . $fileMatlab['name']);
                }
                if (!move_uploaded_file($fileMatlab['tmp_name'], './enviados/'. $urlGet. '/' . $fileMatlab['name'])) {
                    msg('Erro: Arquivo não enviado, erro desconhecido, entrar em contato com o professor!', 'error');
                } else {
                    $f['file'] = $fileMatlab['name'];
                    $f['date'] = date('Y-m-d H:i:s');
                    if (!$readSent = read('act_sent', 'WHERE user_id=? AND order_id=?', [$actUser['id'], $fileMatlab['order_id']])) {
                        $f['user_id'] = $actUser['id'];
                        $f['order_id'] = $fileMatlab['order_id'];
                        create('act_sent', $f);
                        $_SESSION['success'] = 'Exercício enviado com sucesso!';
                        header('Location: ' . BASE . '/' . $urlGet . '/atividades/enviar');
                    } else {
                        foreach ($readSent as $sent)
                            ;
                        if ($sent['grade'] || $actUser['id'] != $sent['user_id']) {
                            header('Location: ' . BASE . '/' . $urlGet . '/atividades/minhas-atividades');
                        } else {
                            $f = array();
                            $f['date'] = date('Y-m-d H:i:s');
                            update('act_sent', $f, 'WHERE id=?', [$sent['id']]);
                            $_SESSION['success'] = 'Exercício re-enviado com sucesso!';
                            header('Location: ' . BASE . '/' . $urlGet . '/atividades/minhas-atividades');
                        }
                    }
                }
            }
        }
        $pag = (urlByIndex(4) == 'page' && urlByIndex(5)) ? $pag = urlByIndex(5) : 1;
        $max = 10;
        $start = ($pag - 1) * $max;
        if (!$readOrdereds = read('act_ordered', 'JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? AND a.id NOT IN (SELECT s.order_id FROM act_sent AS s WHERE s.user_id=?) ORDER BY end_date DESC LIMIT ?, ?', [$class['id'], $actUser['id'], $start, $max])) {
            if ($pag != 1) {
                header('Location: ' . BASE . '/' . $urlGet . '/atividades/enviar');
            } else {
                msg('Você não tem atividades pendentes.', 'infor');
            }
        } else {
            echo '<form name="formSendExec" method="POST" action="" class="formFile" enctype="multipart/form-data">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td>Lista</td>';
            echo '<td>Exercício</td>';
            echo '<td class="text-right">Título</td>';
            echo '<td>Tipo</td>';
            echo '<td>Expira em</td>';
            echo '<td></td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($readOrdereds as $ordered) {
                if ($ordered['end_date'] < date('Y-m-d')) {
                    $classStrike = 'style="text-decoration: line-through;"';
                } else {
                    $classStrike = '';
                }
                if (!read('act_sent', 'WHERE user_id=? AND order_id=?', [$actUser['id'], $ordered['id']])) {
                    $list = getList($ordered['list_id']);
                    $urlList = $list['url'] ? $list['url'] : BASE . '/listas/' . $list['file'];
                    $title = $ordered['title'] ? $ordered['title'] : 'Sem Título';
                    echo '<tr class="infor" ' . $classStrike . '>';
                    echo '<td>' . $list['num'] . ' <a style="font-weight: bold;" href="' . $urlList . '" target="_blank">&#10138;</a></td>';
                    echo '<td>' . addZero($ordered['num']) . '</td>';
                    echo '<td class="text-right">' . $title . '</td>';
                    echo '<td>' . getLanguage($list['type'], 'name') . '</td>';
                    echo '<td>' . date('d/m/Y 23:59', strtotime($ordered['end_date'])) . '</td>';
                    echo '<td>';
                    if ($ordered['end_date'] >= date('Y-m-d')) {
                        echo '<label class="label-file ' . getLanguage($list['type'], 'hl_ref') . '">';
                        echo '<input type="file" id="file" name="fileMatlab[' . $ordered['id'] . ']" onchange="this.form.submit()"/>';
                        echo '</label>';
                        echo '<input type="hidden" name="orderId" value="' . $ordered['id'] . '"/>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '</form>';
            $url = BASE . '/' . $urlGet . '/atividades/enviar/page/';
            paginator('act_ordered', $max, $url, $pag, 'JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? AND a.id NOT IN (SELECT s.order_id FROM act_sent AS s WHERE s.user_id=?)', [$class['id'], $actUser['id']]);
        }
        ?>
    </div>
    <?php
}