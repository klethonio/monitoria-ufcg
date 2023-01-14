<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 2) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor!', 'alert');
} else {
    if (!$readUser = read('act_users', 'WHERE class_id=? AND id=?', [$class['id'], urlByIndex(4)])) {
        header('Location: ' . BASE . '/' . $urlGet . '/orientandos/gerenciar');
    } else {
        $actUser = $readUser[0];
    }
    ?>
    <div id="students">
        <h1>Editar Perfil de <?= $actUser['name'] ?></h1>
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            $f['name'] = uppercaseFirst(trim($post['name']));
            $f['register'] = $post['id'];
            $f['email'] = $post['email'];
            $f['course'] = $post['course'];
            $f['born_date'] = formDate($post['born_date']);
            if (in_array('', $f)) {
                msg('Error: Preencha todos os campos necessários!', 'error');
            } else {
                $ready = TRUE;
                if (!empty($f['email']) && !validMail($f['email'])) {
                    msg('Error: E-mail digitado é invalido!', 'error');
                    $ready = FALSE;
                }
                if ($ready) {
                    if ($post['pswd'] && $post['rePswd'] == $post['pswd']) {
                        $f['pswd'] = encPswd($post['pswd']);
                    }
                    if (read('act_users', 'WHERE class_id=? AND register=?', [$class['id'], $f['register']]) && $f['register'] != $actUser['register']) {
                        msg('Matrícula digitada já cadastrada!', 'error');
                    } elseif (read('act_users', 'WHERE class_id=? AND email=?', [$class['id'], $f['email']]) && $f['email'] != $actUser['email']) {
                        msg('Email digitado já cadastrado!', 'error');
                    } else {
                        update('act_users', $f, 'WHERE class_id=? AND id=?', [$class['id'], urlByIndex(4)]);
                        $_SESSION['success'] = 'Perfil editado com sucesso!';
                        header('Location: ' . BASE . '/' . $urlGet . '/orientandos/gerenciar');
                    }
                }
            }
        }
        ?>
        <form name="formStudent" class="simple-form" method="POST" action="">
            <label>
                <span>Nome</span>
                <input type="text" name="name" value="<?= $actUser['name'] ?>" placeholder="Nome Completo"/>
            </label>
            <label>
                <span>Matrícula</span>
                <input type="text" name="id" value="<?= $actUser['register'] ?>" placeholder="Matrícula do Aluno"/>
            </label>
            <label>
                <span>Email</span>
                <input type="text" name="email" value="<?= $actUser['email'] ?>" placeholder="Email do Aluno (Opcional)"/>
            </label>
            <label>
                <span>Curso</span>
                <input type="text" name="course" value="<?= $actUser['course'] ?>" placeholder="Curso do Aluno"/>
            </label>
            <label>
                <span>Data de Nascimento</span>
                <input type="date" value="<?= $actUser['born_date'] ?>" name="born_date"/>
            </label>
            <hr/>
            <label>
                <span>Nova Senha*</span>
                <input type="password" name="pswd" placeholder="Nova senha para o aluno"/>
            </label>
            <label>
                <span>Repetir Senha*</span>
                <input type="password" name="rePswd" placeholder="Repita a senha digitada"/>
            </label>
            <small class="obs">* Para manter a senha inalterada deixar o campo em branco.</small>
            <input type="submit" name="sendForm" class="btnBlue" value="Editar"/>
        </form>
    </div>
    <?php
}
?>