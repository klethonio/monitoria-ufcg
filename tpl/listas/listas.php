<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
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
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($readLists as $list) {
            $urlList = $list['url'] ? $list['url'] : ($list['file'] ? BASE . '/listas/'.$list['file'] : BASE . '/' . $urlGet . '/listas/listas');
            $descList = !empty($list['description']) ? $list['description'] : 'Sem descrição!';
            $readOrdereds = read('act_ordered', 'WHERE list_id=?', [$list['id']]);
            $qntOrdereds = count($readOrdereds);
            echo '<tr>';
            echo '<td>' . $list['num'] . '</td>';
            echo '<td class="text-right"><a href="' . $urlList . '" target="_blank">' . $descList . '</a></td>';
            echo '<td>' . $list['num_exe'] . '</td>';
            echo '<td>' . $qntOrdereds . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    ?>
</div>
<span class="span-al" id="window-del">
    <p>Atenção: Você está prestes a excluir uma lista, todos os exercicios enviados serão excluídos. Deseja continuar?</p>
    <p style="text-align:center;"><a class="btnRed" name="excluir" href="#">SIM</a> <a class="close-window btnBlue">NÃO</a></p>
</span>
<div class="window-about" id="window-list">
    <span><a class="close-window">Fechar</a></span>
    <h2>Lista <span id="infor-list"></span></h2>
    <p>Informações</p>
    <hr />
    <p>Total de exercícios: <span class="float-right" id="infor-total-exers"></span></p>
    <p>Requisitados: <span class="float-right" id="infor-ordered"></span></p>
    <p>Esperados: <span class="float-right" id="infor-waited"></span></p>
    <p>Recebidos: <span class="float-right" id="infor-total-sent"></span></p>
    <p>Corrigidos: <span class="float-right" id="infor-total-corrected"></span></p>
    <p>Média: <span class="float-right" id="infor-media"></span></p>
</div>
<div class="loading"><img alt="Carregando Informações" src="<?= BASE ?>/tpl/images/loading.gif"/></div>
<div id="mask"></div>