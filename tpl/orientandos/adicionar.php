<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 2) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor!', 'alert');
} else {
    ?>
    <div id="students">
        <h1>Adicionar Estudante ou Monitor</h1>
        <p>Preencha o formulário</p>
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            $f['name'] = uppercaseFirst(trim($post['name']));
            $f['register'] = $post['id'];
            $f['email'] = $post['email'];
            $f['course'] = uppercaseFirst($post['course']);
            $f['born_date'] = formDate($post['born_date']);
            $f['level'] = $post['level'];
            $f['status'] = 1;
            if (in_array('', $f)) {
                msg('Erro: Preencha todos os campos necessários!', 'error');
            } else {
                $ready = TRUE;
                if (!empty($f['email']) && !validMail($f['email'])) {
                    msg('Erro: E-mail digitado é invalido!', 'error');
                    $ready = FALSE;
                }
                if ($ready) {
                    $timestamp = explode('-', $post['born_date']);
                    $f['pswd'] = encPswd($timestamp[2] . $timestamp[1] . $timestamp[0]);
                    $f['class_id'] = $class['id'];
                    if (read('act_users', 'WHERE class_id=? AND register=?', [$class['id'], $f['register']])) {
                        msg('Matrícula digitada já cadastrada!', 'error');
                    } elseif (read('act_users', 'WHERE class_id=? AND email=?', [$class['id'], $f['email']]) && !empty($f['email'])) {
                        msg('Email digitado já cadastrado!', 'error');
                    } else {
                        if (!create('act_users', $f)) {
                            msg('Ocorreu um erro, entre em contato com o desenvolvedor.', 'error');
                        }else{
                            $_SESSION['success'] = 'Estudante cadastrado com sucesso!';
                            header('Location: ' . BASE . '/' . $urlGet . '/orientandos/gerenciar');
                        }
                    }
                }
            }
        }
        ?>
        <form name="formStudent" class="simple-form" method="POST" action="">
            <label>
                <span>Nome</span>
                <input type="text" name="name" value="<?= ($post['name'] ?? null) ?>" placeholder="Nome Completo"/>
            </label>
            <label>
                <span>Matrícula</span>
                <input type="text" name="id" value="<?= ($post['register'] ?? null) ?>" placeholder="Matrícula do Aluno"/>
            </label>
            <label>
                <span>Email</span>
                <input type="text" name="email" value="<?= ($post['email'] ?? null) ?>" placeholder="Email do Aluno (Opcional)"/>
            </label>
            <label>
                <span>Curso</span>
                <input type="text" name="course" value="<?= ($post['course'] ?? null) ?>" placeholder="Curso do Aluno"/>
            </label>
            <label>
                <span>Data de Nascimento</span>
                <input type="date"  value="<?= ($post['born_date'] ?? null) ?>" name="born_date"/>
            </label>
            <label>
                <input type="radio" value="0" checked name="level"/>
                Estudante
            </label>
            <label>
                <input type="radio" value="1" name="level"/>
                Monitor
            </label>
            <small class="obs">* A senha do Aluno será sua data de nascimento e pode ser alterada no menu Perfil do Aluno.</small>
            <input type="submit" name="sendForm" class="btnBlue" value="Cadastrar"/>
        </form>
    </div>
    <?php
}
?>