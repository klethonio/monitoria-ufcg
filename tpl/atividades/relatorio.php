<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    ?>
    <h1>Relatório</h1>
    <div id="activities">
        <div id="tabs">
            <?php
            $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

            if (empty($post['downRel']) && ($post['exers'] == 'all' || !$post['exers'])) {
                if (preg_match('/^(all)\-/i', $post['lists']) || !$post['lists']) {
                    $readLists = read('act_lists', 'WHERE class_id=? ORDER BY num ASC', [$class['id']]);
                } else {
                    $readLists = read('act_lists', 'WHERE class_id=? AND id=? ORDER BY num ASC', [$class['id'], $post['lists']]);
                }
                if (!$readLists) {
                    header('Location: ' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
                } else {
                    if (!$readUsers = read('act_users', 'WHERE class_id=? AND level=0 AND status=1 ORDER BY name', [$class['id']])) {
                        msg('Erro: Nenhum aluno cadastrado!', 'error');
                    } else {
                        $orderExists = TRUE;
                        foreach ($readLists as $list) {
                            $ordereds = array();
                            if ($readOrdered = read('act_ordered', 'WHERE list_id=? ORDER BY num', [$list['id']])) {
                                $hideInfor = TRUE;
                                foreach ($readOrdered as $ordered) {
                                    $ordereds[$ordered['num']] = $ordered['id'];
                                }
                                $lists[] = $list['num'];
                                echo '<table class="tab">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td>Matrícula</td>';
                                echo '<td class="text-right">Nome</td>';
                                foreach ($ordereds as $num => $ordered) {
                                    echo '<td>Ques. ' . $num . '</td>';
                                }
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                $i = 1;
                                foreach ($readUsers as $user) {
                                    $bgTr = '';
                                    if ($i % 2 == 0) {
                                        $bgTr = 'class="bg-tr"';
                                    }
                                    echo '<tr ' . $bgTr . '>';
                                    echo '<td>' . addZero($i) . '</td>';
                                    echo '<td>' . $user['register'] . '</td>';
                                    echo '<td class="text-right">' . $user['name'] . '</td>';
                                    foreach ($ordereds as $ordered) {
                                        $grade = getSent($user['id'], $ordered, 'grade');
                                        $grade = $grade === NULL ? 'Pend.' : $grade;
                                        echo '<td>' . $grade . '</td>';
                                    }
                                    echo '</tr>';
                                    $i++;
                                }
                                echo '</tbody>';
                                echo '</table>';
                            }
                        }
                    }
                    if (!$hideInfor) {
                        msg('Nenhum exercício requisitado nessa lista!', 'infor');
                    }
                }
            } elseif ($post['sendRel']) {
                if (preg_match('/^(all)\-/i', $post['lists']) || !$post['lists']) {
                    $readLists = read('act_lists', 'WHERE class_id=? AND num_exe>=? ORDER BY num ASC', [$class['id'], $post['exers']]);
                } else {
                    $readLists = read('act_lists', 'WHERE class_id=? AND id=? AND num_exe>=? ORDER BY num ASC', [$class['id'], $post['lists'], $post['exers']]);
                }
                if (!$readLists) {
                    header('Location: ' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
                } else {
                    if (!$readUsers = read('act_users', 'WHERE class_id=? AND level=0 AND status=1 ORDER BY name', [$class['id']])) {
                        echo '<tr><td>' . msg('Erro: Nenhum aluno cadastrado!', 'error') . '</td</tr>';
                    } else {
                        foreach ($readLists as $list) {
                            if ($readOrdered = read('act_ordered', 'WHERE list_id=? AND num=?', [$list['id'], $post['exers']])) {
                                $orderExists = TRUE;
                                $lists[] = $list['num'];
                                $ordered = $readOrdered[0];
                                echo '<table class="tab">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td>Matrícula</td>';
                                echo '<td class="text-right">Nome</td>';
                                echo '<td>Ques. ' . addZero($ordered['num']) . '</td>';
                                echo '<td>Comentários</td>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                $i = 1;
                                foreach ($readUsers as $user) {
                                    $sent = getSent($user['id'], $ordered['id']);
                                    $grade = !$sent ? '-' : ($sent['grade'] === null ? 'Pend.' : $sent['grade']);
                                    $bgTr = '';
                                    if ($i % 2 == 0) {
                                        $bgTr = 'class="bg-tr"';
                                    }
                                    echo '<tr ' . $bgTr . '>';
                                    echo '<td>' . addZero($i) . '</td>';
                                    echo '<td>' . $user['register'] . '</td>';
                                    echo '<td class="text-right">' . $user['name'] . '</td>';
                                    echo '<td>' . $grade . '</td>';
                                    echo '<td>' . ($sent['notes'] ?? null) . '</td>';
                                    echo '</tr>';
                                    $i++;
                                }
                                echo '</tbody>';
                                echo '</table>';
                            }
                        }
                    }
                }
                if (!$orderExists) {
                    msg('Nenhum exercício requisato nessa categoria!', 'alert');
                }
            } else {
                header('Location: ' . BASE . '/baixar-relatorio.php?classid=' . $class['id'] . '&list=' . $post['lists'] . '&exer=' . $post['exers']);
            }
            ?>
        </div>
    </div>
    <?php
    if ($lists && $orderExists) {
        echo '<ul id="select-tabs">';
        foreach ($lists as $tabs) {
            echo '<li>Lista ' . $tabs . '</li>';
        }
        echo '</ul>';
    }
    ?>
    <form name="formRel" class="simple-form" method="POST" action="">
        <input type="hidden" name="lists" value="<?= $post['lists'] ?>"/>
        <input type="hidden" name="exers" value="<?= $post['exers'] ?>"/>
        <input type="submit" name="downRel" value="Baixar" class="btnGreen"/>
    </form>
    <?php
}
?>