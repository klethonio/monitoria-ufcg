<h1>Solicitação de Cadastro</h1>
<p>Preencha o formulário</p>
<div id="students">
    <?php
    /* @author Klethônio Ferreira */
    global $urlGet, $class;
    if (!empty($_SESSION['success'])) {
        msg($_SESSION['success'], 'success');
        unset($_SESSION['success']);
    }

    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendForm'])) {
        $f['name'] = uppercaseFirst(trim($post['name']));
        $f['register'] = $post['id'];
        $f['email'] = $post['email'];
        $f['pswd'] = encPswd($post['pswd']);
        $rePswd = $post['rePswd'];
        $f['course'] = uppercaseFirst($post['course']);
        $f['born_date'] = formDate($post['born_date']);
        if (in_array('', $f) || empty($rePswd) || empty($post['born_date'])) {
            msg('Erro: Preencha todos os campos!', 'error');
        } else {
            if (!validMail($f['email'])) {
                msg('Erro: E-mail digitado é invalido!', 'error');
            } elseif ($post['pswd'] != $rePswd) {
                msg('Erro: As senhas digitadas não conferem!', 'error');
            } elseif (read('act_users', 'WHERE register=? AND class_id=?', [$f['register'], $class['id']])) {
                msg('Matrícula digitada já cadastrada!', 'error');
            } elseif (read('act_users', 'WHERE email=? AND class_id=?', [$f['email'], $class['id']]) && !empty($f['email'])) {
                msg('Email digitado já cadastrado!', 'error');
            } else {
                $f['level'] = 0;
                $f['status'] = 0;
                $f['class_id'] = $class['id'];
                if (@create('act_users', $f)) {
                    $_SESSION['success'] = 'Solicitação efetuada com sucesso, seu pedido será avaliado!';
                    header('Location: ' . BASE . '/' . $urlGet . '/cadastro');
                } else {
                    msg('Algum erro ocorreu, tente mais tarde ou entre em contato com nossa equipe!', 'error');
                }
            }
        }
    }
    ?>
    <form name="formStudent" class="simple-form" method="POST" action="">
        <label>
            <span>Nome</span>
            <input type="text" name="name" value="<?php
            if (!empty($f['name'])) {
                echo $f['name'];
            }
            ?>" placeholder="Nome Completo"/>
        </label>
        <label>
            <span>Matrícula</span>
            <input type="text" name="id" value="<?php
            if (!empty($f['register'])) {
                echo $f['register'];
            }
            ?>" placeholder="Matrícula do Aluno"/>
        </label>
        <label>
            <span>Senha</span>
            <input type="password" name="pswd" placeholder="Digite sua senha"/>
        </label>
        <label>
            <span>Repetir Senha</span>
            <input type="password" name="rePswd" placeholder="Repita a senha digitada"/>
        </label>
        <label>
            <span>Email</span>
            <input type="text" name="email" value="<?php
            if (!empty($f['email'])) {
                echo $f['email'];
            }
            ?>" placeholder="Digite um Email para recuperação de senha"/>
        </label>
        <label>
            <span>Curso</span>
            <input type="text" name="course" value="<?php
            if (!empty($f['course'])) {
                echo $f['course'];
            }
            ?>" placeholder="Digite o Curso"/>
        </label>
        <label>
            <span>Data de Nascimente</span>
            <input type="date"  value="<?php
            if (!empty($post['born_date'])) {
                echo $post['born_date'];
            }
            ?>" name="born_date"/>
        </label>
        <input type="submit" name="sendForm" class="btnBlue" value="Solicitar Cadastro"/>
    </form>
    <a href="<?= BASE . '/' . $urlGet ?>/login">Voltar</a>
</div>