<?php
if (!function_exists('getUser')) {
    header('Location: ../');
}
?>
<div class="bloco form" style="display:block">
    <div class="titulo">Adicionar disciplina: <a href="?url=disciplinas/gerenciar" class="btnalt" style="float:right;">Voltar</a></div>
    <?php
    $f = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($f['sendForm'])) {
        unset($f['sendForm']);
        $f['name'] = uppercaseFirst($f['name']);
        $f['url'] = strtolower($f['url']);
        if (in_array('', $f)) {
            echo '<span class="ms al">Preencha todos os campos!</span>';
        } elseif (!preg_match('/[a-z]+/i', $f['url'])) {
            echo '<span class="ms al">Erro: Url digitada é inválida!</span>';
        } else {
            $readDiscipline = read('act_disciplines', 'WHERE name=? OR url=?', [$f['name'], $f['url']]);
            if ($readDiscipline) {
                echo '<span class="ms al">Erro: Disciplina ou url já cadastrada!</span>';
            } else {
                if (create('act_disciplines', $f)) {
                    $_SESSION['return'] = '<span class="ms ok">Disciplina adicionada com sucesso!</span>';
                    header('Location: ?url=disciplinas/gerenciar');
                } else {
                    echo '<span class="ms no">Erro ao adicionar disciplina!</span>';
                }
            }
        }
    }
    ?>
    <form name="formulario" action="" method="post">
        <label class="line">
            <span class="data">Nome:</span>
            <input class="input-pattern" type="text" name="name" value="<?= $f['name'] ?? null ?>" />
            <small class="obs">Ex.: Cálculo Numérico; Introdução a Ciência da Computação.</small>
        </label>
        <label class="line">
            <span class="data">Url:</span>
            <input class="input-pattern" type="text" name="url" value="<?= $f['url'] ?? null ?>" />
            <small class="obs">Ex.: icc; cn; ip.</small>
        </label>
        <input type="reset" value="Limpar" class="btnalt" />
        <input type="submit" value="Adicionar" name="sendForm" class="btn" />
    </form>

</div><!-- /bloco form -->