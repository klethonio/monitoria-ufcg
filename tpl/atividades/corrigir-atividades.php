<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    ?>
    <h1>Corrigir Atividades</h1>

    <?php
    if (!empty($_SESSION['success'])) {
        msg($_SESSION['success'], 'success');
        unset($_SESSION['success']);
    }
    if (!empty($_SESSION['error'])) {
        msg($_SESSION['error'], 'error');
        unset($_SESSION['error']);
    }
    if (!read('act_sent', 'JOIN act_users AS u ON u.id=a.user_id WHERE class_id=?', [$class['id']])) {
        msg('Nenhum exercício enviado!', 'infor');
    } else {
        $pag = (urlByIndex(4) == 'page' && urlByIndex(5)) ? $pag = urlByIndex(5) : 1;
        $max = 20;
        $start = ($pag - 1) * $max;

        $params = array();
        $where = 'JOIN act_ordered AS o ON o.id = a.order_id JOIN act_lists as l ON l.id = o.list_id WHERE l.class_id=? ';
        $params[] = $class['id'];
        $limit = '';
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendSearch'])) {
            foreach ($post['search'] as $key => $value) {
                if ($value) {
                    $search[$key] = $value;
                }
            }
            if ($search['list'] || $search['exer'] || $search['student']) {
                $searched = TRUE;
                if (!empty($search['list'])) {
                    $where .= 'AND o.list_id=? ';
                    $params[] = $search['list'];
                }
                if (!empty($search['exer'])) {
                    $where .= 'AND o.num=? ';
                    $params[] = $search['exer'];
                }
                if (!empty($search['student'])) {
                    $where .= 'AND ';
                    if ($readUsers = read('act_users', 'WHERE class_id=? AND (name LIKE ? OR register=?)', [$class['id'], '%' . $search['student'] . '%', $search['student']])) {
                        $where .= 'a.user_id IN (';
                        foreach ($readUsers as $user) {
                            $where .= '?, ';
                            $params[] = $user['id'];
                        }
                        $where = substr($where, 0, -2).')';
                    } else {
                        $where .= 'a.user_id=? ';
                        $params[] = 'x';
                    }
                }
            }
        } else {
            $params[] = $start;
            $params[] = $max;
            $limit = 'LIMIT ?, ?';
        }
        if (!empty($post['order'])) {
            if ($post['order'] == 'byList') {
                $readSent = read('act_sent', $where . ' ORDER BY a.grade ASC, o.list_id, o.num ASC ' . $limit, $params);
            } elseif ($post['order'] == 'byStud') {
                $readSent = read('act_sent', $where . ' ORDER BY a.grade ASC, a.user_id ' . $limit, $params);
            } else {
                header('Location: ' . BASE . '/' . $urlGet . '/atividades/corrigir-atividades');
            }
        } else {
            $readSent = read('act_sent', $where . ' ORDER BY a.grade IS NULL DESC, o.list_id, o.num ASC ' . $limit, $params);
        }
        if (!empty($searched)) {
            echo '<p>Buscas Relacionadas a: </p>';
            if (!empty($search['list'])) {
                echo '<p>Lista <b>' . getList($search['list'], 'num') . '</b></p>';
            }
            if (!empty($search['exer'])) {
                echo '<p>Exercício <b>' . addZero(intval($search['exer'])) . '</b></p>';
            }
            if (!empty($search['student'])) {
                echo '<p>Aluno <b>' . $search['student'] . '</b></p>';
            }
            echo '<a class="btnBlue" name="restaurar" href="' . BASE . '/' . $urlGet . '/atividades/corrigir-atividades">Restaurar</a>';
        }
        if (!$readSent && $pag != 1) {
            header('Location: ' . BASE . '/' . $urlGet . '/atividades/corrigir-atividades');
        } elseif (!$readSent) {
            msg('Nenhum exercício encontrado nesta busca!', 'infor');
        } else {
            if (empty($searched)) {
                $readLists = read('act_lists', 'WHERE class_id=?', [$class['id']]);
                echo '<p>Selecione a ordem desejada</p>';
                echo '<form name="formSearch" class="search-form" method="POST" action="' . BASE . '/' . $urlGet . '/atividades/corrigir-atividades">';
                echo '<label>';
                echo '<select name="order" onchange="this.form.submit()">';
                echo '<option ';
                if (($post['order'] ?? null) == 'byList') {
                    echo 'selected';
                }
                echo ' value="byList">Por Lista (Padrão)</option>';
                echo '<option ';
                if (($post['order'] ?? null) == 'byStud') {
                    echo 'selected';
                }
                echo ' value="byStud">Por Aluno</option>';
                echo '</select>';
                echo '</label>';
                echo '<p>Pesquisa avançada</p>';
                echo '<label>';
                echo '<span>Lista</span>';
                echo '<select style="width: 200px;" name="search[list]">';
                echo '<option value="">--- LISTA ---</option>';
                foreach ($readLists as $list) {
                    echo '<option value="' . $list['id'] . '">Lista ' . $list['num'] . '</option>';
                }
                echo '</select>';
                echo '</label>';
                echo '<label>';
                echo '<span>Exercício</span>';
                echo '<input type="number" name="search[exer]" placeholder="Número do exercício"/>';
                echo '</label>';
                echo '<label>';
                echo '<span>Nome ou matrícula</span>';
                echo '<input type="text" name="search[student]" placeholder="Matrícula ou nome do aluno"/>';
                echo '</label>';
                echo '<input type="submit" name="sendSearch" class="btnBlue" value="Buscar"/>';
                echo '</form>';
            }
            echo '<div id="activities">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td>Lista</td>';
            echo '<td>Exer.</td>';
            echo '<td class="text-right">Aluno</td>';
            echo '<td class="text-right">Título</td>';
            echo '<td>Tipo</td>';
            echo '<td>Corrigido Por</td>';
            echo '<td>Nota</td>';
            echo '<td></td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            $pastList = null;
            foreach ($readSent as $sent) {
                $exer = getExer($sent['order_id']);
                $title = $exer['title'] ? resText($exer['title'], 30) : 'Sem Título';
                $grade = validFloat($sent['grade']) ? $sent['grade'] : 'Pendente';
                $trClass = !validFloat($sent['grade']) ? 'alert' : ($sent['grade'] >= 7 ? 'success' : 'error');
                $iconCorr = $trClass == 'alert' ? 'corr' : 'recorr';

                if ($pastList != $exer['list_id']) {
                    $readList = read('act_lists', 'WHERE id=?', [$exer['list_id']]);
                    $list = $readList[0];
                    $urlList = $list['url'] ? $list['url'] : ($list['file'] ? BASE . '/listas/' . $list['file'] : BASE . '/' . $urlGet . '/listas/listas');
                    $pastList = $exer['list_id'];
                }

                $user = getUser($sent['user_id']);
                $name = explode(' ', trim($user['name']));
                $name = $name[0] . ' ' . end($name);

                if ($monitorName = getUser($sent['corrected_by'], 'name')) {
                    $monitorName = explode(' ', $monitorName);
                    $monitorName = $monitorName[0];
                } else {
                    $monitorName = $sent['corrected_by'] === NULL ? '' : 'Monitor não encontrador!';
                }

                echo '<tr class="' . $trClass . '">';
                echo '<td>' . $list['num'] . ' <a style="font-weight: bold;" href="' . $urlList . '" target="_blank">&#10138;</a></td>';
                echo '<td>' . addZero($exer['num']) . '</td>';
                echo '<td class="text-right">' . $name . ' - ' . $user['register'] . '</td>';
                echo '<td class="text-right">' . $title;
                echo ' </td>';
                echo '<td>' . getLanguage($list['type'], 'name') . ' </td>';
                echo '<td>' . $monitorName . '</td>';
                echo '<td>' . $grade . '</td>';
                echo '<td style="width:100px;"><a title="Corrigir Exercício" href="' . BASE . '/' . $urlGet . '/atividades/corrigir/' . $sent['id'] . '"><img alt="Corrigir Exercício" src="' . BASE . '/tpl/images/' . $iconCorr . '.png"/></a> <a title="Visualizar Exercício" href="' . BASE . '/' . $urlGet . '/atividades/visualizar/' . $sent['id'] . '"><img alt="Visualizar Exercício" src="' . BASE . '/tpl/images/view.png"/></a> <a title="Baixar Exercício" href="' . BASE . '/' . $urlGet . '/atividades/baixar/' . $sent['id'] . '" target="_blank"><img alt="Baixar Exercício" src="' . BASE . '/tpl/images/down.png"/></a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            if (empty($post['sendSearch'])) {
                $url = BASE . '/' . $urlGet . '/atividades/corrigir-atividades/page/';
                paginator('act_sent', $max, $url, $pag, 'JOIN act_users AS u ON u.id=a.user_id WHERE class_id=?', [$class['id']]);
            }
        }
    }
}
?>