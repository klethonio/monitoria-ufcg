<?php
/* @author Klethônio Ferreira */
global $actUser, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!read('act_users', 'WHERE class_id=? AND id=?', [$class['id'], $actUser['id']])) {
    header('Location: ' . BASE . '/' . $urlGet . '/home');
} else {
    $name = explode(' ', $actUser['name']);
    $name = $name[0];
}
?>
<div id="ordinary">
    <h1>Olá, <?= $name ?>!</h1>
    <?php
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendForm'])) {
        if (empty($post['pswd']) && !empty($post['email'])) {
            if (read('act_users', 'WHERE class_id=? AND email=? AND id<>?', [$class['id'], $post['email'], $actUser['id']])) {
                msg('Erro: E-mail já cadastrado!', 'error');
            } else {
                update('act_users', array('email' => $post['email']), 'WHERE id=?', [$actUser['id']]);
                $_SESSION['actUser']['email'] = $post['email'];
                msg('E-mail alterado com sucesso!', 'success');
            }
        } elseif (empty($post['pswd']) || $post['pswd'] != $post['rePswd']) {
            msg('Erro: Campos em branco e/ou senhas não conferem!', 'error');
        } elseif ($actUser['pswd'] != encPswd($post['oldPswd'])) {
            msg('Erro: Senha antiga não confere!', 'error');
        } else {
            $f['email'] = $actUser['email'];
            if (read('act_users', 'WHERE class_id=? AND email=? AND id<>?', [$class['id'], $post['email'], $actUser['id']]) && $f['email'] != $post['email']) {
                msg('Erro: E-mail já cadastrado!', 'error');
            } elseif (!empty($f['email']) && !validMail($f['email'])) {
                msg('Erro: E-mail digitado é invalido!', 'error');
            } else {
                $f['email'] = $post['email'];
            }
            $f['pswd'] = encPswd($post['pswd']);
            update('act_users', $f, 'WHERE id=?', [$actUser['id']]);
            $_SESSION['actUser']['email'] = $f['email'];
            $_SESSION['actUser']['pswd'] = $f['pswd'];
            msg('Senha e/ou email alterados com sucesso!', 'success');
        }
    }
    $actUser = getUser($actUser['id']);
    ?>
    <form name="formStudent" class="simple-form" method="POST" action="">
        <label>
            <span>Nome Completo</span>
            <input type="text" disabled name="name" value="<?= $actUser['name'] ?>"/>
        </label>
        <label>
            <span>Matrícula</span>
            <input type="text" disabled name="id" value="<?= $actUser['register'] ?>"/>
        </label>
        <label>
            <span>E-mail</span>
            <input type="text" name="email" value="<?= $actUser['email'] ?>" placeholder="E-mail para recuperação de senha"/>
        </label>
        <label>
            <span>Curso</span>
            <input type="text" disabled name="course" value="<?= $actUser['course'] ?>"/>
        </label>
        <label>
            <span>Data de Nascimente</span>
            <input type="date" disabled value="<?= $actUser['born_date'] ?>" name="born_date"/>
        </label>
        <hr/>
        <label>
            <span>Nova Senha</span>
            <input type="password" name="pswd" placeholder="Digite sua nova senha"/>
        </label>
        <label>
            <span>Repetir Nova Senha</span>
            <input type="password" name="rePswd" placeholder="Repita a senha digitada"/>
        </label>
        <hr />
        <label>
            <span>Senha Antiga</span>
            <input type="password" name="oldPswd" placeholder="Digite sua antiga senha para alterar"/>
        </label>
        <input type="submit" name="sendForm" class="btnBlue" value="Editar"/>
    </form>
</div>