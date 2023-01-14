<?php
if (!function_exists('getUser')) {
    header('Location: ./');
}
?>
<div class="bloco home" style="display:block">
    <?php
    if ($readDeleteds = read('act_config_class', 'WHERE expiration IS NOT NULL')) {
        foreach ($readDeleteds as $deleted) {
            $days = strtotime($deleted['expiration']) - strtotime(date('Y-m-d'));
            $days = (($days / 60) / 60) / 24;
            $days = $days == 1 ? $days . ' dia' : $days . ' dias';
            echo '<span class="ms al">A turma ' . $deleted['class'] . ' - ' . $deleted['period'] . ' será excluída em ' . $days . '!</span>';
        }
    }
    ?>
    <div class="titulo">Dados Gerais:</div>
    <div style="width:554px; height:200px; line-height: 200px; text-align: center; float:left; border:3px solid #CCC; margin-bottom:15px; display: none;">Em desenvolvimento!</div>
    <table width="100%" border="0" class="tbdados" cellspacing="0" cellpadding="0">
        <?php
        $disciplineChanged = 0;
        $readDisciplines = read('act_disciplines', 'ORDER BY name');
        if ($readDisciplines) {
            foreach ($readDisciplines as $discipline) {
                echo '<tr class="ses">';
                echo '<td colspan="2">Turmas de ' . $discipline['name'] . ':</td>';
                echo '</tr>';
                $readClasses = read('act_config_class', 'WHERE discipline_id=?', [$discipline['id']]);
                $contActive = 0;
                if ($readClasses) {
                    foreach ($readClasses as $class) {
                        if ($class['status'] == 1) {
                            $contActive++;
                        }
                    }
                    echo '<tr><td>Ativas:</td><td>' . $contActive . '</td></tr>';
                    echo '<tr><td>Total:</td><td>' . count($readClasses) . '</td></tr>';
                } else {
                    echo '<td colspan="2">Nenhuma turma cadastrada!</td>';
                }
            }
        }
        ?>
        <tr class="ses">
            <td colspan="2">Alunos (Turmas Ativas):</td>
        </tr>
        <?php
        $readClasses = read('act_config_class', 'WHERE status=1 ORDER BY period DESC, discipline_id, class');
        if ($readClasses) {
            foreach ($readClasses as $class) {
                $readUsers = read('act_users', 'WHERE class_id=? AND status=1', [$class['id']]);
                echo '<tr>';
                echo '<td>' . $class['period'] . ' - Turma ' . $class['class'] . ' - ' . getDiscipline($class['discipline_id'], 'name') . '</td>';
                echo '<td>' . count($readUsers) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="2">Nenhuma turma ativa!</td></tr>';
        }
        ?>
    </table><!-- /tbdados -->
</div><!-- /bloco home -->