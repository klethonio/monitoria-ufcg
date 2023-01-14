<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 0, TRUE) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um aluno!', 'alert');
} else {
    ?>
    <div id="activities">
        <h1>Atividades Enviadas</h1>
        <?php
        if (!empty($_SESSION['success'])) {
            msg($_SESSION['success'], 'success');
            unset($_SESSION['success']);
        }
        if (!empty($_SESSION['error'])) {
            msg($_SESSION['error'], 'error');
            unset($_SESSION['error']);
        }
        if (!$readSents = read('act_sent', 'WHERE user_id=? ORDER BY date DESC', [$actUser['id']])) {
            msg('Nenhuma atividade enviada!', 'infor');
        } else {
            echo '<form name="formSendExec" method="POST" action="' . BASE . '/' . $urlGet . '/atividades/enviar" class="formFile" enctype="multipart/form-data">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td>Lista</td>';
            echo '<td>Exer.</td>';
            echo '<td class="text-right">Título</td>';
            echo '<td>Tipo</td>';
            echo '<td></td>';
            echo '<td>Nota</td>';
            echo '<td></td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($readSents as $sent) {
                $exer = getExer($sent['order_id']);
                $title = $exer['title'] ? $exer['title'] : 'Sem Título';
                $grade = validFloat($sent['grade']) ? $sent['grade'] : 'Pendente';
                $trClass = !$sent['grade'] ? 'alert' : ($sent['grade'] >= 7 ? 'success' : 'error');

                $list = getList($exer['list_id']);
                $urlList = $list['url'] ? $list['url'] : BASE . '/listas/' . $list['file'];

                echo '<tr class="' . $trClass . '">';
                echo '<td>' . $list['num'] . ' <a style="font-weight: bold;" href="' . $urlList . '" target="_blank">&#10138;</a></td>';
                echo '<td>' . addZero($exer['num']) . '</td>';
                echo '<td class="text-right">' . $title . '</td>';
                echo '<td>' . getLanguage($list['type'], 'name') .'</td>';
                echo '<td>';
                echo ' <a title="Visualizar Exercício" href="' . BASE . '/' . $urlGet . '/atividades/visualizar/' . $sent['id'] . '"><img alt="Visualizar Exercício" src="' . BASE . '/tpl/images/view.png"/></a>';
                echo '</td>';
                echo '<td>' . $grade . '</td>';
                echo '<td>';
                if (!$sent['grade'] && getExer($sent['order_id'], 'end_date') >= date('Y-m-d')) {
                    echo '<label class="label-file ' . getLanguage($list['type'], 'hl_ref') . '">';
                    echo '<input type="file" id="file" name="fileMatlab[' . $sent['order_id'] . ']" onchange="this.form.submit()"/>';
                    echo '</label>';
                    echo '<input type="hidden" name="orderId" value="' . $sent['order_id'] . '"/>';
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</form>';
        }
        ?>
    </div>
    <?php
}
?>