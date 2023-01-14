<?php
if (!function_exists('getUser')) {
    header('Location: ../');
}
$disciplineId = $_GET['discid'];
if ($readDisciplines = read('act_disciplines', 'WHERE id=?', [$disciplineId])) {
    $discipline = $readDisciplines[0];
} else {
    header('Location: ?url=disciplinas/gerenciar');
}
?>
<div class="bloco form" style="display:block;">
    <div class="titulo">Editar disciplina <span style="color: #900;"><?= strtoupper($discipline['url']) ?></span>: <a href="?url=disciplinas/gerenciar" class="btnalt" style="float:right;">Voltar</a></div>
    <?php
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendForm'])) {
        $f['name'] = $post['name'];
        $f['url'] = $post['url'];

        if (in_array('', $f)) {
            echo '<span class="ms al">Preencha todos os campos!</span>';
        } elseif (!preg_match('/[a-z]+/i', $f['url'])) {
            echo '<span class="ms al">Erro: Url digitada é inválida!</span>';
        } else {
            $readDiscipline = read('act_disciplines', 'WHERE (name=? OR url=?) AND id<>?', [$f['name'], $f['url'], $discipline['id']]);
            if ($readDiscipline) {
                echo '<span class="ms al">Erro: Disciplina ou url já cadastrada!</span>';
            } else {
                update('act_disciplines', $f, 'WHERE id=?', [$discipline['id']]);
                $_SESSION['return'] = '<span class="ms ok">Disciplina editada com sucesso!</span>';
                header('Location: ?url=disciplinas/gerenciar');
            }
        }
    }
    ?>
    <form name="formulario" action="" method="post">
        <label class="line">
            <span class="data">Nome:</span>
            <input class="input-pattern" type="text" name="name" value="<?= $discipline['name'] ?>" />
            <small class="obs">Ex.: Cálculo Numérico; Introdução a Ciência da Computação.</small>
        </label>
        <label class="line">
            <span class="data">Url:</span>
            <input class="input-pattern" type="text" name="url" value="<?= $discipline['url'] ?>" />
            <small class="obs">Ex.: icc; cn; ip.</small>
        </label>
        <input type="reset" value="Limpar" class="btnalt" />
        <input type="submit" value="Adicionar" name="sendForm" class="btn" />
    </form>
</div>