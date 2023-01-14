<?php
/* @author Klethônio Ferreira */
global $url, $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 2) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor!', 'alert');
} else {
    ?>
    <h1>Lista de Orientandos</h1>
    <div id="students" class="limit-height">
        <?php
        if (!empty($_SESSION['success'])) {
            msg($_SESSION['success'], 'success');
            unset($_SESSION['success']);
        }

        if (!$readUsers = read('act_users', 'WHERE class_id=? AND level <> 2 ORDER BY level DESC, status ASC, name ASC', [$class['id']])) {
            msg('Nenhum estudante cadastrado!', 'infor');
        } else {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td>ID</td>';
            echo '<td>Matrícula</td>';
            echo '<td class="text-right">Nome</td>';
            echo '<td class="text-right">Curso</td>';
            echo '<td class="text-right">Nível</td>';
            echo '<td></td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($readUsers as $actUser) {
                $level = $actUser['level'] == 1 ? 'Monitor' : 'Aluno';
                $level .= $actUser['status'] == 0 ? ' <b>(Inativo)</b>' : '';
                echo '<tr>';
                echo '<td>' . $actUser['id'] . '</td>';
                echo '<td>' . $actUser['register'] . '</td>';
                echo '<td class="text-right">' . $actUser['name'] . '</td>';
                echo '<td class="text-right">' . $actUser['course'] . '</td>';
                echo '<td class="text-right">' . $level . '</td>';
                echo '<td>';
                if ($actUser['status'] == 0) {
                    echo '<a title="Ativar Usuário" href="#window-atv" rel="' . BASE . '/' . $urlGet . '/orientandos/gerenciar/ativar/' . $actUser['id'] . '"><img alt="Ativar Usuário" src="' . BASE . '/tpl/images/actv.png"/></a> ';
                }
                echo '<a title="Editar Usuário" href="' . BASE . '/' . $urlGet . '/orientandos/editar/' . $actUser['id'] . '"><img alt="Editar Usuário" src="' . BASE . '/tpl/images/edit.png"/></a> <a title="Deletar Usuário"  href="#window-del" rel="' . BASE . '/' . $urlGet . '/orientandos/gerenciar/excluir/' . $actUser['id'] . '"><img alt="Deletar Usuário" src="' . BASE . '/tpl/images/del.png"/></a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        ?>
    </div>
    <span class="span-al" id="window-del">
        <p>Atenção: Você está prestes a excluir um aluno, todos os exercícios enviados por ele serão excluidos. Deseja continuar?</p>
        <p style="text-align:center;"><a class="btnRed" name="excluir" href="#">SIM</a> <a class="close-window btnBlue">NÃO</a></p>
    </span>
    <span class="span-al" id="window-atv">
        <p>Tem certeza que deseja ativar esse usuário?</p>
        <p style="text-align:center;"><a class="btnBlue" name="ativar" href="#">SIM</a> <a class="close-window btnRed">NÃO</a></p>
    </span>
    <div id="mask"></div>
    <?php
    if (urlByIndex(4) == 'excluir') {
        delete('act_users', 'WHERE id=? AND level <> 2', [urlByIndex(5)]);
        delete('act_sent', 'WHERE user_id=?', [urlByIndex(5)]);
        $_SESSION['success'] = 'Estudante excluido com sucesso!';
        header('Location:' . BASE . '/' . $urlGet . '/orientandos/gerenciar');
    } elseif (urlByIndex(4) == 'ativar') {
        update('act_users', array('status' => 1), 'WHERE id=?', [urlByIndex(5)]);
        $readAdmin = read('act_admin');
        $admin = $readAdmin[0];
        $readUser = read('act_users', 'WHERE id=?', [urlByIndex(5)]);
        $user = $readUser[0];
        $message = '<div style="font:\'Trebuchet MS\', Arial, Helvetica, sans-serif;">';
        $message .= '<h3 style="color:#099;">Prezado(a) ' . $user['name'] . '.</h3>';
        $message .= '<p style="color:#666">Estamos entrando em contato pois sua conta no sistema de Atividades ICC foi ativa. Se digira ao site clicando no link abaixo para verificar se você tem atividades pendentes.</p><hr/>';
        $message .= '<p style="color:#069"><em><a href="' . BASE . '/' . $urlGet . '">Conectar Agora</a></em></p><hr/>';
        $message .= '<h3 style="color:#900;">Atenciosamente, <strong>Equipe de ' . SITENAME . '</strong></h3>';
        $message .= '<p style="color:#666; font-size:12px;">enviada em: ' . date('d/m/Y H:i:s') . '</p></div>';

        sendMail('Conta Ativada - Turma ' . addZero($class['class']) . ' - Atividades ICC ', $message, $admin['pswd_gmail'], $admin['gmail'], MAILNAME, $user['email'], $user['name'], 'no-reply@progexercicios.dsc.ufcg.edu.br');

        $_SESSION['success'] = 'Estudante ativado com sucesso!';
        header('Location:' . BASE . '/' . $urlGet . '/orientandos/gerenciar');
    }
}
?>