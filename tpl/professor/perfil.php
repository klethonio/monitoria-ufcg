<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!checkUser($actUser['id'], 2) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor!', 'alert');
} else {
    if (!read('act_users', 'WHERE class_id=? AND id=?', [$class['id'], $actUser['id']])) {
        header('Location: ' . BASE . '/' . $urlGet . '/home');
    } else {
        $name = explode(' ', $actUser['name']);
        $name = $name[0];
    }
    ?>
    <div id="students">
        <h1>Olá, <?= $name ?>!</h1>
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            $f['name'] = uppercaseFirst($post['name']);
            $f['register'] = $post['id'];
            $f['email'] = $post['email'];
            $f['born_date'] = $post['born_date'];
            if (in_array('', $f)) {
                msg('Erro: Preencha todos os campos necessários', 'error');
            } else {
                $pswd = encPswd($post['oldPswd']);
                if (!read('act_users', 'WHERE pswd=?', [$pswd])) {
                    msg('Erro: Senha antiga incorreta, digite para efetuar as alterações!', 'error');
                } elseif (read('act_users', 'WHERE class_id=? AND register=? AND id<>?', [$class['id'], $f['register'], $actUser['id']])) {
                    msg('Erro: Matrícula já cadastrada!', 'error');
                } elseif (read('act_users', 'WHERE class_id=? AND email=? AND id<>?', [$class['id'], $f['email'], $actUser['id']]) && $f['email']) {
                    msg('Erro: E-mail já cadastrado!', 'error');
                } else {
                    if ($post['pswd']) {
                        if ($post['pswd'] != $post['rePswd']) {
                            msg('Erro: Campo Nova Senha e Repetir Senha não conferem!', 'error');
                        } else {
                            $f['pswd'] = encPswd($post['pswd']);
                            $_SESSION['actUser']['pswd'] = $f['pswd'];
                            update('act_users', $f, 'WHERE id=?', [$actUser['id']]);
                            msg('Senha e/ou email alterados com sucesso!', 'success');
                        }
                    } else {
                        update('act_users', $f, 'WHERE id=?', [$actUser['id']]);
                        msg('Senha e/ou email alterados com sucesso!', 'success');
                    }
                    $_SESSION['actUser']['name'] = $f['name'];
                    $_SESSION['actUser']['register'] = $f['register'];
                    $_SESSION['actUser']['email'] = $f['email'];
                    $_SESSION['actUser']['born_date'] = $f['born_date'];
                }
            }
        }
        ?>
        <form name="formStudent" class="simple-form" method="POST" action="">
            <label>
                <span>Nome Completo</span>
                <input type="text" name="name" value="<?= ($f['name'] ?? $actUser['name']) ?>"/>
            </label>
            <label>
                <span>Matrícula</span>
                <input type="text" name="id" value="<?= ($f['register'] ?? $actUser['register']) ?>"/>
            </label>
            <label>
                <span>E-mail</span>
                <input type="text" name="email" value="<?= ($f['email'] ?? $actUser['email']) ?>" placeholder="E-mail para recuperação de senha"/>
            </label>
            <label>
                <span>Data de Nascimento</span>
                <input type="date" value="<?= ($post['born_date'] ?? $actUser['born_date']) ?>" name="born_date"/>
            </label>
            <hr/>
            <label>
                <span>Nova Senha*</span>
                <input type="password" name="pswd" placeholder="Digite sua nova senha"/>
            </label>
            <label>
                <span>Repetir Nova Senha</span>
                <input type="password" name="rePswd" placeholder="Repita a senha digitada"/>
            </label>
            <hr />
            <label>
                <span>Senha Antiga</span>
                <input type="password" name="oldPswd" placeholder="Digite sua antiga senha para alterar os dados"/>
            </label>
            <small class="obs">*Para manter a senha inalterada, deixe os dois campos em branco.</small>
            <input type="submit" name="sendForm" class="btnBlue" value="Editar"/>
        </form>
    </div>
    <?php
}
?>