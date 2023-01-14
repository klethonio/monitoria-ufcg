<?php
if (!function_exists('getUser')) {
    header('Location: ../');
}
?>
<div class="bloco cat" style="display:block">
    <div class="titulo">Turmas: <a href="?url=turmas/abrir-turma" title="Criar nova turma" class="btn" style="float:right;">Abrir Turma</a></div>   
    <?php
    if (!empty($_SESSION['return'])) {
        echo $_SESSION['return'];
        unset($_SESSION['return']);
    } else {
        if ($readDeleteds = read('act_config_class', 'WHERE expiration IS NOT NULL')) {
            foreach ($readDeleteds as $deleted) {
                $days = strtotime($deleted['expiration']) - strtotime(date('Y-m-d'));
                $days = (($days / 60) / 60) / 24;
                $days = $days == 1 ? $days . ' dia' : $days . ' dias';
                echo '<span class="ms al">A turma ' . $deleted['class'] . ' - ' . $deleted['period'] . ' será excluída em ' . $days . '!</span>';
            }
        }
    }
    $pag = $_GET['pag'] ?? 1;
    $max = 10;
    $start = ($pag - 1) * $max;

    $classId = filter_input(INPUT_GET, 'delclass', FILTER_DEFAULT);

    if ($classId) {
        if (!read('act_config_class', 'WHERE id=?', [$classId])) {
            echo '<span class="ms al">Turma não encontrada!</span>';
        } else {
            $f['status'] = 0;
            $f['expiration'] = date('Y-m-d', strtotime("+30 days"));
            update('act_config_class', $f, 'WHERE id=?', [$classId]);
            $_SESSION['return'] = '<span class="ms ok">Turma DESATIVADA com sucesso!</span><span class="ms al">A turma será exluída dentro de 30 dias!</span>';
            header('Location: ?url=turmas/gerenciar&pag=' . $pag);
        }
    }

    $status = filter_input(INPUT_GET, 'status', FILTER_DEFAULT);

    if ($status) {
        $classData = explode('-', $status);
        if (!empty($classData[1])) {
            if ($classData[0] == 1) {
                $f['status'] = 0;
                $_SESSION['return'] = '<span class="ms ok">Turma DESATIVADA com sucesso!</span>';
            } else {
                $f['status'] = 1;
                $f['expiration'] = NULL;
                $_SESSION['return'] = '<span class="ms ok">Turma ATIVADA com sucesso!</span>';
            }
            update('act_config_class', $f, 'WHERE id=?', [$classData[1]]);
        }
        header('Location: ?url=turmas/gerenciar&pag=' . $pag);
    }

    if ($readClasses = read('act_config_class', 'ORDER BY period DESC, id DESC LIMIT ?, ?', [$start, $max])) {
        ?>
        <table width="560" border="0" class="tbdados" style="float:left;" cellspacing="0" cellpadding="0">
            <tr class="ses">
                <td>ID:</td>
                <td>Disc.:</td>
                <td>Nº:</td>
                <td>Período:</td>
                <td>Professor:</td>
                <td>Status:</td>
                <td colspan="2">Ações:</td>
            </tr>
            <?php
            foreach ($readClasses as $class) {
                $urlDiscipline = getDiscipline($class['discipline_id'], 'url');
                $classStatus = $class['status'] == 1 ? 'ok.png' : 'off.png';
                ?>
                <tr>
                    <td><?= $class['id'] ?></td>
                    <td><?= strtoupper($urlDiscipline) ?></td>
                    <td><a href="<?= BASE . '/' . $class['period'] . '/' . $urlDiscipline . '-' . $class['class'] ?>" target="_blank"><?= $class['class'] ?></a></td>
                    <td><?= $class['period'] ?></td>
                    <td><?= getUser($class['professor_id'], 'name') ?></td>
                    <td align="center"><a title="Ativar/Desativar" href="#window-atv" rel="?url=turmas/gerenciar&status=<?= $class['status'] . '-' . $class['id'] ?>" title="editar"><img src="ico/<?= $classStatus ?>" alt="<?= $class['status'] ?>" title="<?= $class['status'] ?>" /></a></td>
                    <td align="center"><a href="?url=turmas/editar-turma&classid=<?= $class['id'] ?>" title="editar"><img src="ico/edit.png" alt="editar" title="editar turma <?= $class['class'] ?>" /></a></td>
                    <td align="center"><a href="#window-del" rel="?url=turmas/gerenciar&pag=<?= $pag ?>&delclass=<?= $class['id'] ?>" title="Deletar"><img src="ico/no.png" alt="excluir" title="deletar turma <?= $class['class'] ?>" /></a></td>
                </tr>
                <?php
            }
            echo '</table>';
            $url = '?url=turmas/gerenciar&pag=';
            paginator('act_config_class', $max, $url, $pag, 'ORDER BY period DESC, id DESC');
            ?>
    </div><!-- /bloco cat -->
    <span class="ms al" id="window-del">
        <p>Atenção: Você está prestes a excluir uma turma. Deseja continuar?</p>
        <p style="text-align:center;"><a class="btnalt" name="excluir" href="#">SIM</a> <a class="close-window btn">NÃO</a></p>
    </span>
    <span class="ms al" id="window-atv">
        <p>Tem certeza que realizar está operação?</p>
        <p style="text-align:center;"><a class="btn" name="ativar" href="#">SIM</a> <a class="close-window btnalt">NÃO</a></p>
    </span>
    <div id="mask"></div>
    <?php
} elseif ($pag == 1) {
    echo '<span class="ms in">Não existe categorias!</span>';
} else
    header('Location: ?url=posts/categories');
?>
</div>