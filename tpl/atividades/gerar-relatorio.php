<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}

if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    ?>
    <div id="activities">
        <h1>Gerar relatórios</h1>
        <?php
        if (!$readLists = read('act_lists', 'WHERE class_id=?', [$class['id']])) {
            msg('Nenhuma lista cadastrada!', 'infor');
        } else {
            if ($readNumExers = read('act_ordered', 'JOIN act_lists AS l ON l.id=a.list_id WHERE l.class_id=? GROUP BY a.num', [$class['id']])) {
                foreach ($readNumExers as $numExer) {
                    $numExers[] = $numExer['num'];
                }
            }
            ?>
            <form name="formRel" class="simple-form" method="POST" target="_blank" action="<?= BASE . '/' . $urlGet ?>/atividades/relatorio">
                <label>
                    <span>Selecione a Lista</span>
                    <select name="lists">
                        <option value="all-<?=$class['id']?>">Todas</option>
                        <?php
                        foreach ($readLists as $list) {
                            echo '<option value="' . $list['id'] . '">Lista ' . $list['num'] . '</option>';
                        }
                        ?>
                    </select>
                </label>
                <label>
                    <span>Selecione o exercício</span>
                    <select name="exers">
                        <?php
                        if ($numExers) {
                            echo '<option value="all">Todos</option>';
                            foreach ($numExers as $numExer) {
                                echo "<option value=\"{$numExer}\">Num {$numExer}</option>";
                            }
                        } else {
                            echo "<option disabled value=\"\">Nenhum exercício encontrado!</option>";
                        }
                        ?>
                    </select>
                </label>
                <div id="loading">Carregando</div>
                <input type="submit" name="sendRel" class="btnBlue" value="Visualizar"/>
                <input type="submit" name="downRel" value="Baixar" class="btnGreen"/>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}
?>