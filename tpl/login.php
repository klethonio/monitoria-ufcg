<?php
/* @author Klethônio Ferreira */

global $url, $urlGet, $class;

if (!empty($_SESSION['actUser'])) {
    header('Location: ' . BASE . '/' . $urlGet . '/home');
}
if (!empty($_SESSION['success'])) {
    msg($_SESSION['success'], 'success');
    unset($_SESSION['success']);
}
if (!urlByIndex(3)) {
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if (!empty($post['sendForm'])) {
        if (!$readUser = read('act_users', 'WHERE register=? and class_id=?', [$post['nReg'], $class['id']])) {
            msg('Error: Usuário não encontrado!', 'error');
        } else {
            $actUser = $readUser[0];
            if ($actUser['pswd'] != encPswd($post['pswd'])) {
                msg('Erro: Senha incorreta!', 'error');
            } elseif ($actUser['status'] == 0) {
                msg('Seu cadastro ainda está inativo, aguarde confirmação!', 'alert');
            } else {
                $_SESSION['actUser'] = $actUser;
                setcookie('actUser', 'true', time() + 60 * 15, '/');
                $_SESSION['login'] = TRUE;
                header('Location: ' . BASE . '/' . $urlGet . '/home');
            }
        }
    } else {
        msg('Você não está conectado ao sistema. Utilize o formulário abaixo para se autenticar.', 'infor');
    }
    ?>
    <form name="formLogin" class="simple-form" method="POST" action="">
        <label>
            <span>Matrícula:</span>
            <input type="text" name="nReg" class="input" placeholder="Número da Matricula"/>
        </label>
        <label>
            <span>Senha:</span>
            <input type="password" name="pswd" class="input" placeholder="Senha"/>
        </label>
        <small class="block"><a href="<?= BASE . '/' . $urlGet ?>/cadastro">Solicitar cadastro</a> | <a href="<?= BASE . '/' . $urlGet ?>/login/recuperar">Esqueceu sua senha?</a></small>
        <input type="submit" name="sendForm" class="btnBlue" value="Entrar"/>
    <!--    <p><a href="#">Ajuda e Suporte</a></p>-->
    </form>
    <?php
} elseif (urlByIndex(3) == 'recuperar' && urlByIndex(4) != 'validar') {
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if (!empty($post['sendForm'])) {
        if (!$readUser = read('act_users', 'WHERE class_id=? AND (register=? OR email=?)', [$class['id'], $post['reg'], $post['reg']])) {
            msg('Error: Usuário não encontrado!', 'error');
        } else {
            $readAdmin = read('act_admin');
            $admin = $readAdmin[0];
            $user = $readUser[0];
            $key = sha1(uniqid(mt_rand(), true));
            update('act_users', array('code' => $key), 'WHERE email=?', [$user['email']]);
            $linkRec = BASE . '/' . $urlGet . '/login/recuperar/validar/' . $key;
            $message = '<div style="font:\'Trebuchet MS\', Arial, Helvetica, sans-serif;">';
            $message .= '<h3 style="color:#099;">Prezado(a) ' . $user['name'] . ', recupere seu acesso!</h3>';
            $message .= '<p style="color:#666">Estamos entrando em contato pois foi solicitado em nosso sistema a recupereção de dados de acesso. Para concluir o processo, caso essa operação tenha sido efetuada por você, clique no link abaixo ou caso tenha problemas, cole a url no seu navegador!!</p><hr/>';
            $message .= '<p style="color:#069"><em><a href="' . $linkRec . '">' . $linkRec . '</a></em></p><hr/>';
            $message .= '<h3 style="color:#900;">Atenciosamente, <strong>Equipe de ' . SITENAME . '</strong></h3>';
            $message .= '<p style="color:#666; font-size:12px;">enviada em: ' . date('d/m/Y H:i:s') . '</p></div>';

            if (sendMail('Recupere seus dados - Atividades ICC Turma ' . addZero($class['class']), $message, $admin['pswd_gmail'], $admin['gmail'], MAILNAME, $user['email'], $user['name'], 'no-reply@progexercicios.dsc.ufcg.edu.br')) {
                $_SESSION['success'] = 'Um e-mail foi enviado para <b>' . $actUser['email'] . '</b> com instruções para o resgate da senha. Favor, verifque caixa de spam!';
                header('Location: ' . BASE . '/' . $urlGet . '/login/recuperar');
            } else {
                echo base64_decode($admin['pswd_gmail']);
                msg('Erro: Operação não realizada, entre em contato com nossa equipe!', 'error');
            }
        }
    } else {
        msg('Para recuperar sua senha digite seu e-mail cadastrado ou matrícula.', 'infor');
    }
    ?>
    <form name="formLogin" class="simple-form" method="POST" action="">
        <label>
            <span>Indentificação:</span>
            <input type="text" name="reg" class="input" placeholder="Matricula ou E-mail"/>
        </label>
        <input type="submit" name="sendForm" class="btnBlue" value="Recuperar"/>
        <small class="obs">Você receberar um e-mail com as instruções para regaste de senha.</small>
    <!--    <p><a href="#">Ajuda e Suporte</a></p>-->
    </form>
    <a href="<?= BASE . '/' . $urlGet ?>/login">Voltar</a>
    <?php
} elseif (urlByIndex(3) == 'recuperar' && urlByIndex(4) == 'validar') {
    $key = urlByIndex(5);
    if ($readUser = read('act_users', 'WHERE code=?', [$key])) {
        $f = array();
        $user = $readUser[0];
        $timestamp = explode('-', $user['born_date']);
        $pswd = $timestamp[2] . $timestamp[1] . $timestamp[0];
        $f['pswd'] = encPswd($pswd);
        $f['code'] = NULL;
        update('act_users', $f, 'WHERE class_id=? AND email=?', [$class['id'], $user['email']]);
        $_SESSION['success'] = 'Sua senha foi alterada e sua nova senha é: <b>' . $pswd . '</b>. Modifique-a assim que possível!';
        header('Location: ' . BASE . '/' . $urlGet . '/login/recuperar');
    } else {
        msg('Chave de validação invalida! Por favor, reenvie o formulario de ativação. Caso o erro persista entre em contato com nossa equipe.', 'error');
    }
    echo '<a href="' . BASE . '/' . $urlGet . '/login">Voltar</a>';
} else {
    header('Location: ' . BASE . '/' . $urlGet . '/login');
}