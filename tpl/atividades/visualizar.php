<?php
/* @author Klethônio Ferreira */
global $actUser, $url, $urlGet, $class;
require('Classes/Encoding.php');
if (!$readSent = read('act_sent', 'WHERE id=?', [urlByIndex(4)])) {
    header('Location: ' . BASE . '/' . $urlGet . '/atividades/minhas-atividades');
} else {
    $sent = $readSent[0];
    if ($sent['user_id'] != $actUser['id'] && $actUser['level'] == 0) {
        header('Location: ' . BASE . '/' . $urlGet . '/atividades/minhas-atividades');
    } else {
        $exer = getExer($sent['order_id']);
    }
}
$list = getList($exer['list_id']);
?>
<h1>Exercício <?= addZero($exer['num']) ?> - Lista <?= $list['num'] ?> - <?= $exer['title'] ?></h1>
<p>Enviado em: <?= date('d/m/Y H:i', strtotime($sent['date'])) ?></p>
<div id="activities">
    <div id="code">
        <a class="float" title="Baixar Exercício" href="<?= BASE . '/' . $urlGet ?>/atividades/baixar/<?= urlByIndex(4) ?>" target="_blank"><img alt="Baixar Exercício" src="<?= BASE ?>/tpl/images/down.png"/></a>
        <?php
        $language = getLanguage($list['type']);
        if ($language['readable'] == 1) {
            $lines = file('./enviados/' . $urlGet . '/' . $sent['file']);
            $sent['file'] = Encoding::fixUTF8(trim(implode('', $lines)));
            $geshi = new GeSHi($sent['file'], $language['hl_ref']);
            $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
            $geshi->set_overall_style('font: 10px normal normal 90% monospace; color: #000066; border: 1px solid #d0d0d0; background-color: #f0f0f0;', false);
            $geshi->set_line_style('background: #fcfcfc;', 'background: #f5f5f5;');
            $geshi->set_line_style('color: #003030;', 'font-weight: bold; color: #006060;', true);
            $geshi->set_code_style('color: #000000;', true);

            echo $geshi->parse_code();
        } else {
            echo '<div class="notes">Não é possível vizualizar este exercício, por favor, baixar!</div>';
        }
        ?>
    </div>
</div>
<h4>Notas sobre a correção</h4>
<div class="notes">
    <?php
    if ($sent['notes']) {
        echo nl2br($sent['notes']);
    } else {
        echo 'Nenhuma nota sobre a correção foi registrada!';
    }
    ?>
</div>