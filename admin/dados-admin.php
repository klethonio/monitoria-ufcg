<?php
if (!function_exists('getUser')) {
    header('Location: ./');
}
?>
<div class="bloco form" style="display:block">
    <div class="titulo">Editar dados do administrador: <a href="./" class="btnalt" style="float:right;">Voltar</a></div>
    <?php
    $readAdmin = read('act_admin', 'LIMIT 1');
    $admin = $readAdmin[0];
    
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($f['sendForm'])) {
        $f['email'] = $post['email'];
        $currentPswd = encPswd($post['currentPswd']);
        if (!empty($post['pswd'])) {
            $f['pswd'] = encPswd($post['pswd']);
            $rePswd = encPswd($post['rePswd']);
        }
        if (empty($f['email'])) {
            echo '<span class="ms al">Preencha todos os campos necessários!</span>';
        } elseif (!validMail($f['email'])) {
            echo '<span class="ms no">E-mail informado é invalido!</span>';
        } elseif (!empty($post['pswd']) && $f['pswd'] != $rePswd) {
            echo '<span class="ms no">Novas senhas informadas não conferem!</span>';
        } elseif ($currentPswd != $admin['pswd']) {
            echo '<span class="ms no">Senha atual incorreta!</span>';
        } else {
            update('act_admin', $f, 'LIMIT 1', array());
            $_SESSION['adUser']['email'] = $f['email'];
            if (!empty($post['pswd'])) {
                $_SESSION['adUser']['pswd'] = $f['pswd'];
            }
            echo '<span class="ms ok">Dados editados com sucesso!</span>';
        }
    }
    $readAdmin = read('act_admin', 'LIMIT 1');
    $admin = $readAdmin[0];
    ?>                
    <form name="formulario" action="" method="post" enctype="multipart/form-data">
        <label class="line">
            <span class="data">E-mail do Administrador:</span>
            <input class="input-pattern" type="text" name="email" value="<?= $admin['email'] ?>" />
        </label>
        <label class="line">
            <span class="data">Nova Senha:</span>
            <input class="input-pattern" type="password" name="pswd" value="" />
            <span class="obs">Obs.: Para manter a senha inalterada, deixe o campo em branco!</span>
        </label>
        <label class="line">
            <span class="data">Repitir senha:</span>
            <input class="input-pattern" type="password" name="rePswd" value="" />
            <span class="obs">Obs.: Para manter a senha inalterada, deixe o campo em branco!</span>
        </label>
        <label class="line">
            <span class="data">Senha atual:</span>
            <input class="input-pattern" type="password" name="currentPswd" value="" />
        </label>
        <input type="reset" value="Limpar" class="btnalt" />
        <input type="submit" value="Editar" name="sendForm" class="btn" />

    </form>

</div><!-- /bloco form -->