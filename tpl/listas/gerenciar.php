<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser monitor ou professor!', 'alert');
} else {
    ?>
    <h1>Listas de Exercícios</h1>
    <div id="lists">
        <?php
        if (!empty($_SESSION['success'])) {
            msg($_SESSION['success'], 'success');
            unset($_SESSION['success']);
        }
/////////////////////////////////////////
/////Excluir LISTA
/////////////////////////////////////////
        if (urlByIndex(4) == 'excluir' && $actUser['level'] != 2) {
            msg('Para efetuar essa operação você precisa ser um professor!', 'alert');
        } elseif (urlByIndex(4) == 'excluir') {
            if ($readOrdered = read('act_ordered', 'WHERE list_id=?', [urlByIndex(4)])) {
                foreach ($readOrdered as $ordered) {
                    $readSents = read('act_sent', 'WHERE order_id=?', [$ordered['id']]);
                    foreach ($readSents as $sent) {
                        unlink('./enviados/' . $urlGet . '/' . $sent['file']);
                    }
                    delete('act_sent', 'WHERE order_id=?', [$ordered['id']]);
                    delete('act_ordered', 'WHERE id=?', [$ordered['id']]);
                }
            }
            $listDirectory = './listas/Ref-' . $class['id'] . '_Lista_' . getList(urlByIndex(5), 'num') . '.pdf';
            if (file_exists($listDirectory)) {
                unlink($listDirectory);
            }
            delete('act_lists', 'WHERE class_id=? AND id=?', [$class['id'], urlByIndex(5)]);
            $_SESSION['success'] = 'Lista excluida com sucesso!';
            header('Location:' . BASE . '/' . $urlGet . '/listas/gerenciar');
        }
/////////////////////////////////////////

        if (!$readLists = read('act_lists', 'WHERE class_id=? ORDER BY num', [$class['id']])) {
            msg('Nenhuma lista adicionada!', 'infor');
        } else {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td>Referência</td>';
            echo '<td>Exercícios(Qnt.)</td>';
            echo '<td class="text-right">Descrição</td>';
            echo '<td class="text-right">Tipo</td>';
            echo '<td></td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($readLists as $list) {
                $urlList = $list['url'] ? $list['url'] : ($list['file'] ? BASE . '/listas/' . $list['file'] : BASE . '/' . $urlGet . '/listas/listas');
                $descList = !empty($list['description']) ? $list['description'] : 'Sem descrição!';
                echo '<tr>';
                echo '<td>' . $list['num'] . '</td>';
                echo '<td>' . $list['num_exe'] . '</td>';
                echo '<td class="text-right"><a href="' . $urlList . '" target="_blank">' . $descList . '</a></td>';
                echo '<td class="text-right">' . getLanguage($list['type'], 'name') . '</td>';
                echo '<td><a title="Sobre a Lista" href="#window-list" rel="' . $list['id'] . '"><img alt="Sobre o Exercício" src="' . BASE . '/tpl/images/info.png"/></a> <a title="Requisitar Exercícios" href="' . BASE . '/' . $urlGet . '/atividades/requisitar/' . $list['id'] . '"><img alt="Requisitar Exercícios" src="' . BASE . '/tpl/images/add.png"/></a> <a title="Editar Lista" href="' . BASE . '/' . $urlGet . '/listas/editar/' . $list['id'] . '"><img alt="Editar Lista" src="' . BASE . '/tpl/images/edit.png"/></a> <a title="Deletar Lista" href="#window-del" rel="' . BASE . '/' . $urlGet . '/listas/gerenciar/excluir/' . $list['id'] . '"><img alt="Deletar Lista" src="' . BASE . '/tpl/images/del.png"/></a></td>';
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
    <?php
}
?>