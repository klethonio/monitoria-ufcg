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

    if (!empty($post['sendForm'])) {
        $f['gmail'] = $post['gmail'];
        if (!empty($post['pswd'])) {
            $f['pswd_gmail'] = base64_encode($post['pswd']);
        }
        if (empty($f['gmail'])) {
            echo '<span class="ms al">Preencha todos os campos necessários!</span>';
        } elseif (!validMail($f['gmail'])) {
            echo '<span class="ms no">E-mail informado é invalido!</span>';
        } else {
            update('act_admin', $f, 'LIMIT 1', array());
            echo '<span class="ms ok">Dados editados com sucesso!</span>';
        }
    }
    $readAdmin = read('act_admin', 'LIMIT 1');
    $admin = $readAdmin[0];
    ?>                
    <form name="formulario" action="" method="post" enctype="multipart/form-data">
        <label class="line">
            <span class="data">Gmail para envios:</span>
            <input class="input-pattern" type="text" name="gmail" value="<?= $admin['gmail'] ?>" />
        </label>
        <label class="line">
            <span class="data">Atualizar Senha:</span>
            <input class="input-pattern" type="password" name="pswd" value="" />
            <span class="obs">Obs.: Essa senha serve para enviar e-mails para os alunos via gmail, deve ser a senha padrão do gmail acima.</span>
        </label>
        <input type="reset" value="Limpar" class="btnalt" />
        <input type="submit" value="Editar" name="sendForm" class="btn" />

    </form>

</div><!-- /bloco form -->