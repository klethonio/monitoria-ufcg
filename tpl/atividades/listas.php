<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 0, TRUE) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um aluno!', 'alert');
} else {
    ?>
    <div id="lists">
        <h1>Listas de Exercícios</h1>
        <?php
        if (!$readLists = read('act_lists', 'WHERE class_id=?', [$class['id']])) {
            msg('Nenhuma lista adicionada!', 'infor');
        } else {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td>Referência</td>';
            echo '<td class="text-right">Descrição</td>';
            echo '<td>Exercícios(Qnt.)</td>';
        echo '<td>Requisitados</td>';
            echo '<td></td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($readLists as $list) {
                $urlList = $list['url'] ? $list['url'] : BASE . '/listas/' . $list['file'];
                $descList = !empty($list['description']) ? $list['description'] : 'Sem descrição!';
                $readOrdereds = read('act_ordered', 'WHERE list_id=?', [$list['id']]);
                $qntOrdereds = count($readOrdereds);
                echo '<tr>';
                echo '<td>' . $list['num'] . '</td>';
                echo '<td class="text-right"><a href="' . $urlList . '" target="_blank">' . $descList . '</a></td>';
                echo '<td>' . $list['num_exe'] . '</td>';
            echo '<td>' . $qntOrdereds . '</td>';
                echo '<td><a title="Baixar Lista" href="' . $urlList . '" download><img alt="Baixar Lista" src="' . BASE . '/tpl/images/down.png"/></a>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        ?>
    </div>
    <?php
}
?>