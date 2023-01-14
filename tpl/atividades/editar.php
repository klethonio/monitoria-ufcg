<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    if (!$readExer = read('act_ordered', 'JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? AND a.id=?', [$class['id'], urlByIndex(4)])) {
        header('Location: ' . BASE . '/' . $urlGet . '/atividades/requisitadas');
    } else {
        $exer = $readExer[0];
        $readList = read('act_lists', 'WHERE id=?', [$exer['list_id']]);
        $list = $readList[0];
    }
    ?>
    <div id="activities">
        <h1>Editar Atividade Requisitada - Lista <?= $list['num'] ?> / Exercício <?= addZero($exer['num']) ?></h1>
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
             $f['title'] = $post['title'];
             $f['end_date'] = formDate($post['endDate']);
            if (in_array('', $f)) {
                msg('Erro: Preencha todos os campos.', 'error');
            } elseif ($f['end_date'] < date('Y-m-d H:i:s')) {
                msg('Erro: A data selecionada já passou! Selecione uma data no futuro ou o dia de hoje.', 'error');
            } else {
                update('act_ordered', $f, 'WHERE id=?', [$exer['id']]);
                $_SESSION['success'] = 'Exercício editado com sucesso!';
                header('Location: ' . BASE . '/' . $urlGet . '/atividades/requisitadas');
            }
        }
        ?>
        <form name="formExec" class="simple-form" method="POST" action="">
            <label>
                <span>Título</span>
                <input type="text" name="title" value="<?= $exer['title'] ?>" placeholder="Digite um novo títuo"/>
            </label>
            <label>
                <span>Nova Data Limite</span>
                <input type="date" name="endDate" value="<?= date('Y-m-d', strtotime($exer['end_date'])) ?>" />
            </label>
            <input type="submit" name="sendForm" class="btnBlue" value="Editar"/>
        </form>
    </div>
    <?php
}
?>