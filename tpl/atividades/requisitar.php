<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    if (!$readList = read('act_lists', 'WHERE id=?', [urlByIndex(4)])) {
        header('Location: ' . BASE . '/' . $urlGet . '/listas/gerenciar');
    } else {
        $list = $readList[0];
    }
    ?>
    <h1>Requisitar Exercicios - Lista <?= $list['num'] ?></h1>
    <p>Selecione os Exercícios e escolhe a data limite de entrega</p>
    <div id="activities">
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            $exers = $post['exers'];
            $f['list_id'] = $list['id'];
            $f['date'] = date('Y-m-d');
            $f['end_date'] = formDate($post['endDate']);
            if (empty($exers) || empty($f['end_date'])) {
                msg('Erro: Selecione no mínimo 1 exercício e/ou preencha a data de expiração!', 'error');
            } elseif ($f['end_date'] < date('Y-m-d H:i:s')) {
                msg('Erro: A data selecionada já passou! Selecione uma data no futuro ou o dia de hoje.', 'error');
            } else {
                foreach ($exers as $num => $value) {
                    if (!read('act_ordered', 'WHERE list_id=? AND num=?', [$list['id'], $num])) {
                        $f['num'] = $num;
                        $f['title'] = !$post['titles'][$num] ? NULL : (mb_strlen($post['titles'][$num], 'utf-8') > 50 ? mb_substr($post['titles'][$num], 0, 50, 'utf-8') : $post['titles'][$num]);
                        create('act_ordered', $f);
                        $success = TRUE;
                    }
                }
                if ($success) {
                    $sentMail = FALSE;
                    if ($post['sendMail']) {
                        $readAdmin = read('act_admin');
                        $admin = $readAdmin[0];
                        $readUsers = read('act_users', 'WHERE class_id=? AND level=0 AND status=1', [$class['id']]);
                        foreach ($readUsers as $user) {
                            $message = '<h3 style="color:#099;">Prezado(a) ' . $user['name'] . '.</h3>';
                            $message .= '<div style="font:\'Trebuchet MS\', Arial, Helvetica, sans-serif;">';
                            $message .= '<p style="color:#666">Estamos entrando em contato pois houve uma solicitação recente de exercícios para serem enviados:</p>';
                            $message .= '<h2 style="color:#666;"><strong>Lista ' . $list['num'] . ' - '. $list['description'] .'</strong></h2>';
                            $message .= '<p style="color:#666;">Exercício(s): ' . implode(', ', array_keys($exers)) . '</p>';
                            $message .= '<p style="color:#666;">';
                            foreach ($exers as $num => $value) {
                                $message .= $num . (!$post['titles'][$num] ? '' : ' - ' . $post['titles'][$num]);
                            }
                            $message .= '</p>';
                            $message .= '<small>Data limite de envio: ' . date('d/m/Y 23:59', strtotime($f['end_date'])) . '</small>';
                            $message .= '<hr/><p style="color:#069"><em><a href="' . BASE . '">Enviar Agora</a></em></p><hr/>';
                            $message .= '<h3 style="color:#900;">Atenciosamente, <strong>Equipe de ' . SITENAME . '</strong></h3>';
                            $message .= '<p style="color:#666; font-size:12px;">enviada em: ' . date('d/m/Y H:i:s') . '</p></div>';

                            if (sendMail('Novas Atividades - ICC Turma ' . addZero($class['class']), $message, $admin['pswd_gmail'], $admin['gmail'], MAILNAME, $user['email'], $user['name'], 'no-reply@progexercicios.dsc.ufcg.edu.br')) {
                                $sentMail = TRUE;
                            }
                        }
                    }
                    if ($sentMail) {
                        $_SESSION['success'] = 'Exercícios requisitados com sucesso!';
                    } else {
                        $_SESSION['success'] = 'Exercícios requisitados com sucesso!<b> Porém o e-mail não foi enviado. Envie manualemente ou entre em contato com o desenvolvedor para verficar falhas.</b>';
                    }
                    echo $_SESSION['success'];
                    header('Location: ' . BASE . '/' . $urlGet . '/listas/gerenciar');
                }
            }
        }
        ?>
        <form name="formExec" class="simple-form" method="POST" action="">
            <?php
            $readExers = read('act_ordered', 'WHERE list_id=?', [$list['id']]);
            
            for ($num = 1; $num <= $list['num_exe']; $num++) {
                // $ordered = FALSE;
                // if ($readExer = read('act_ordered', 'WHERE list_id=? AND num=?', [$list['id'], $num])) {
                //     $ordered = TRUE;
                //     $exer = $readExer[0];
                // }
                ?>
                <label style="position: relative">
                    <input <?= (in_array($num, array_column($readExers, 'num')) ? 'checked disabled' : (!empty($post['exers'][$num]) ? 'checked' : '')) ?> type="checkbox" name="exers[<?= $num ?>]" value="1"/>
                    <?= addZero($num) ?>
                    <input <?= (in_array($num, array_column($readExers, 'num')) ? 'disabled value="' . $readExers[array_search($num, array_column($readExers, 'num'))]['title'] . '"' : (!empty($post['exers'][$num]) ? 'value="' . $post['titles'][$num] . '"' : '')) ?> type="text" name="titles[<?= $num ?>]" class="max-char" maxlength="50" placeholder="Titulo do Exercício(Opcional)"/>
                    <small class="count-char">Restantes: 50</small>
                </label>
                <?php
            }
            ?>
            <label>
                <span>Expira em</span>
                <input type="date" name="endDate" value="<?= ($post['end_date'] ?? null) ?>" />
            </label>
            <label>
                <input type="checkbox" name="sendMail" value="1"/>
                Enviar e-mail para cada aluno
            </label>
            <small class="obs">Obs.: Para editar ou excluir atividades já requesitadas acesse o menu em "Atividades" > "Atividades Requisitadas".</small>
            <input type="submit" name="sendForm" class="btnBlue" value="Requisitar"/>
        </form>
    </div>
    <?php
}
?>