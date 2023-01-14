<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('checkUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    ?>
    <h1>Exercícios Requisitados</h1>
    <?php
    if (!empty($_SESSION['success'])) {
        msg($_SESSION['success'], 'success');
        unset($_SESSION['success']);
    }

    $params = array();
    $where = ' JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? ';
    $params[] = $class['id'];
    $limit = '';

    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendSearch'])) {
        foreach ($post['search'] as $key => $value) {
            if ($value) {
                $search[$key] = $value;
            }
        }
        if ($search['list']) {
            $searched = TRUE;
            $where .= 'AND a.list_id=? ';
            $params[] = $search['list'];
            if ($search['exer']) {
                $where .= 'AND a.num=? ';
                $params[] = $search['exer'];
            }
        }
    } else {
        $pag = (urlByIndex(4) == 'page' && urlByIndex(5)) ? $pag = urlByIndex(5) : 1;
        $max = 10;
        $start = ($pag - 1) * $max;

        $params[] = $start;
        $params[] = $max;
        $limit = 'LIMIT ?, ?';
    }
    $readOrdereds = read('act_ordered', $where . 'ORDER BY a.end_date DESC ' . $limit, $params);
    if (!empty($searched)) {
        echo '<p>Buscas Relacionadas a: </p>';
        if ($search['list']) {
            echo '<p>Lista <b>' . getList($search['list'], 'num') . '</b></p>';
        }
        if ($search['exer']) {
            echo '<p>Exercício <b>' . addZero(intval($search['exer'])) . '</b></p>';
        }
        echo '<a class="btnBlue" name="restaurar" href="' . BASE . '/' . $urlGet . '/atividades/requisitadas">Restaurar</a>';
    }
    if (!$readOrdereds && $pag && $pag != 1) {
        header('Location: ' . BASE . '/' . $urlGet . '/atividades/requisitadas');
    } elseif (!$readOrdereds) {
        msg('Nenhum exercício requisitado!', 'infor');
    } else {
        if (empty($searched)) {
            $readLists = read('act_lists', 'WHERE class_id=?', [$class['id']]);
            echo '<form name="formSearch" class="search-form" method="POST" action="">';
            echo '<p>Pesquisa avançada</p>';
            echo '<label>';
            echo '<span>Lista</span>';
            echo '<select style="width: 200px;" name="search[list]">';
            foreach ($readLists as $list) {
                echo '<option value="' . $list['id'] . '">Lista ' . $list['num'] . '</option>';
            }
            echo '</select>';
            echo '</label>';
            echo '<label>';
            echo '<span>Exercício</span>';
            echo '<input type="number" name="search[exer]" placeholder="Número do exercício"/>';
            echo '</label>';
            echo '<input type="submit" name="sendSearch" class="btnBlue" value="Buscar"/>';
            echo '</form>';
        }
        echo '<div id="lists">';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<td>ID</td>';
        echo '<td>Lista</td>';
        echo '<td>Exer.</td>';
        echo '<td class="text-right">Título</td>';
        echo '<td>Tipo</td>';
        echo '<td>Criada em</td>';
        echo '<td></td>';
        echo '</tr>';
        echo '</thead>';
        $dates = array();
        $tbodyFirst = false;
        foreach ($readOrdereds as $ordered) {
            $title = $ordered['title'] ? $ordered['title'] : 'Sem título';
            $list = getList($ordered['list_id']);
            $urlList = $list['url'] ? $list['url'] : ($list['file'] ? BASE . '/listas/' . $list['file'] : BASE . '/' . $urlGet . '/listas/listas');
            if (!in_array($ordered['end_date'], $dates)) {
                if ($ordered['end_date'] < date('Y-m-d')) {
                    $classStrike = 'style="text-decoration: line-through;"';
                } else {
                    $classStrike = '';
                }
                if ($tbodyFirst) {
                    echo '</tbody>';
                }
                echo '<thead class="thead-date">';
                echo '<tr>';
                echo '<td colspan="7" ' . $classStrike . ' class="text-right">Expiram em ' . date('d/m/Y 23:59', strtotime($ordered['end_date'])) . '</td>';
                echo '</tr>';
                echo '</thead>';
                $dates[] = $ordered['end_date'];
                $tbodyFirst = true;
                echo '<tbody>';
            }
            echo '<tr>';
            echo '<td>' . $ordered['id'] . '</td>';
            echo '<td>' . $list['num'] . ' <a style="font-weight: bold;" href="' . $urlList . '" target="_blank">&#10138;</a></td>';
            echo '<td>' . addZero($ordered['num']) . '</td>';
            echo '<td class="text-right">' . $title . '</td>';
            echo '<td>' . getLanguage($list['type'], 'name') . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($ordered['date'])) . '</</td>';
            echo '<td><a title="Sobre o Exercício" href="#window-order" rel="' . $ordered['id'] . '"><img alt="Sobre o Exercício" src="' . BASE . '/tpl/images/info.png"/></a> <a title="Editar Exercício" href="' . BASE . '/' . $urlGet . '/atividades/editar/' . $ordered['id'] . '"><img alt="Editar Exercício" src="' . BASE . '/tpl/images/edit.png"/></a> <a title="Deletar Exercício" href="#window-del" rel="' . BASE . '/' . $urlGet . '/atividades/requisitadas/excluir/' . $ordered['id'] . '"><img alt="Deletar Exercício" src="' . BASE . '/tpl/images/del.png"/></a></td>';
            echo '</tr>';
        }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    }
    if (empty($post['sendSearch'])) {
        $url = BASE . '/' . $urlGet . '/atividades/requisitadas/page/';
        paginator('act_ordered', $max, $url, $pag, ' JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? ', [$class['id']]);
    }
    ?>
    <span class="span-al" id="window-del">
        <p>Atenção: Você está prestes a excluir um exercício, todas as atividades enviadas serão excluídas. Deseja continuar?</p>
        <p style="text-align:center;"><a class="btnRed" name="excluir" href="#">SIM</a> <a class="close-window btnBlue">NÃO</a></p>
    </span>
    <div class="window-about" id="window-order">
        <span><a class="close-window">Fechar</a></span>
        <h2>Exercicio <span id="infor-exer"></span> - Lista <span id="infor-list"></span></h2>
        <p>Informações</p>
        <hr />
        <p>Expira em: <span class="float-right" id="infor-end-date"></span></p>
        <p>Total de Alunos: <span class="float-right" id="infor-total-users"></span></p>
        <p>Recebidos: <span class="float-right" id="infor-total-sent"></span></p>
        <p>Corrigidos: <span class="float-right" id="infor-total-corrected"></span></p>
        <p>Média: <span class="float-right" id="infor-media"></span></p>
    </div>
    <div class="loading"><img alt="Carregando Informações" src="<?= BASE ?>/tpl/images/loading.gif"/></div>
    <div id="mask"></div>
    <?php
    $url = explode('/', $_GET['url']);
    if (urlByIndex(4) == 'excluir') {
        delete('act_ordered', 'WHERE id=?', [urlByIndex(5)]);
        $readSents = read('act_sent', 'WHERE order_id=?', [urlByIndex(5)]);
        foreach($readSents as $sent){
            unlink('./enviados/' . $urlGet . '/' . $sent['file']);
        }
        delete('act_sent', 'WHERE order_id=?', [urlByIndex(5)]);
        $_SESSION['success'] = 'Atividade excluida com sucesso!';
        header('Location:' . BASE . '/' . $urlGet . '/atividades/requisitadas');
    }
}