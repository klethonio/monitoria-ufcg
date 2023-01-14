<?php
if (!function_exists('getUser')) {
    header('Location: ../');
}
?>
<div class="bloco cat" style="display:block">
    <div class="titulo">Disciplinas: <a href="?url=disciplinas/adicionar-disciplina" title="Criar nova turma" class="btn" style="float:right;">Adicionar Disciplina</a></div>   
    <?php
    if (!empty($_SESSION['return'])) {
        echo $_SESSION['return'];
        unset($_SESSION['return']);
    }
    
    $disciplineId = filter_input(INPUT_GET, 'deldisc', FILTER_DEFAULT);

    if ($disciplineId) {
        if (!read('act_disciplines', 'WHERE id=?', [$disciplineId])) {
            echo '<span class="ms al">Disciplina não encontrada!</span>';
        } elseif (read('act_config_class', 'WHERE discipline_id=?', [$disciplineId])) {
            echo '<span class="ms no">Erro: Uma disciplina não pode conter turmas para que a mesma seja deletada!</span>';
        } else {
            delete('act_disciplines', 'WHERE id=?', [$disciplineId]);
            $_SESSION['return'] = '<span class="ms ok">Disciplina deletada com sucesso!</span>';
            header('Location: ?url=disciplinas/gerenciar');
        }
    }
    if ($readDisciplines = read('act_disciplines')) {
        ?>
        <table width="560" border="0" class="tbdados" style="float:left;" cellspacing="0" cellpadding="0">
            <tr class="ses">
                <td>ID:</td>
                <td>Disciplina:</td>
                <td>Url:</td>
                <td colspan="2">Ações:</td>
            </tr>
            <?php
            foreach ($readDisciplines as $discipline) {
                ?>
                <tr>
                    <td><?= $discipline['id'] ?></td>
                    <td><?= $discipline['name'] ?></td>
                    <td><?= $discipline['url'] ?></td>
                    <td align="center"><a href="?url=disciplinas/editar-disciplina&discid=<?= $discipline['id'] ?>" title="editar"><img src="ico/edit.png" alt="editar" title="editar disciplina <?= strtoupper($discipline['url']) ?>" /></a></td>
                    <td align="center"><a href="#window-del" rel="?url=disciplinas/gerenciar&deldisc=<?= $discipline['id'] ?>" title="Deletar"><img src="ico/no.png" alt="excluir" title="deletar disciplina <?= strtoupper($discipline['url']) ?>" /></a></td>
                </tr>
                <?php
            }
            echo '</table>';
            ?>
    </div><!-- /bloco cat -->
    <span class="ms al" id="window-del">
        <p>Atenção: Você está prestes a excluir uma disciplina. Deseja continuar?</p>
        <p style="text-align:center;"><a class="btnalt" name="excluir" href="#">SIM</a> <a class="close-window btn">NÃO</a></p>
    </span>
    <div id="mask"></div>
    <?php
} else {
    echo '<span class="ms in">Não existem disiciplinas!</span>';
}
?>
</div>