<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
require('Classes/Encoding.php');
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 1) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor ou monitor!', 'alert');
} else {
    if (!$readSent = read('act_sent', 'JOIN act_users AS u ON u.id=a.user_id WHERE u.class_id=? AND a.id=?', [$class['id'], urlByIndex(4)])) {
        header('Location: ' . BASE . '/' . $urlGet . '/atividades/corrigir-atividades');
    } else {
        $sent = $readSent[0];
        $exer = getExer($sent['order_id']);

        $user = getUser($sent['user_id']);
        $name = explode(' ', $user['name']);
        $name = $name[0] . ' ' . $name[count($name) - 1];
        if ($exer['end_date'] < date('Y-m-d H:i:s')) {
            $expired = TRUE;
        }
        $list = getList($exer['list_id']);
        $urlList = $list['url'] ? $list['url'] : ($list['file'] ? BASE . '/listas/' . $list['file'] : BASE . '/' . $urlGet . '/listas/listas');
    }INPUT_POST
    ?>
    <h1><a style="font-weight: bold;" href="<?= $urlList ?>" target="_blank">&#10138;</a> Corrigir Exercício <?= addZero($exer['num']) ?> - Lista <?= getList($exer['list_id'], 'num') ?> - <?php
        if (!empty($exer['title'])) {
            echo $exer['title'];
        } else {
            echo 'Sem titulo';
        }
        ?></h1>
    <p>Aluno(a) <b><?= $name ?> - <?= $user['register'] ?></b></p>
    <?php
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendForm'])) {
        $f['notes'] = $post['notes'] ? $post['notes'] : NULL;
        if (!validFloat($post['grade'])) {
            msg('Erro: Digite uma nota válida!', 'error');
        } elseif ($post['grade'] < 0 || $post['grade'] > 10) {
            msg('Erro: Digite uma nota entre 0 e 10!', 'error');
        } elseif ($f['notes'] && mb_strlen($f['notes'], 'utf8') > 250) {
            msg('Erro: Campo Notas aceita no máximo 250 caracteres!', 'error');
        } else {
            $f['grade'] = floatval(str_replace(',', '.', $post['grade']));
            $f['corrected_by'] = $actUser['id'];
            if ($actUser['register'] == '118210680') {
                $cond = 'WHERE id=? OR (file=? AND order_id=? AND (corrected_by=? OR grade IS NULL))';
//                $f['corrected_by'] = ;
                $params = [$sent['id'], $sent['file'], $sent['order_id'], $actUser['id']];
            } else {
                $cond = 'WHERE id=?';
                $params = [$sent['id']];
            }

            $return = update('act_sent', $f, $cond, $params);
            $_SESSION['success'] = 'Nota registrada com sucesso!';
            if ($actUser['register'] == '118210680') {
                $return = !empty($return) ? $return : 0;
                $_SESSION['success'] .= ' ' . $return . ' exercício(s) corrigido(s)';
            }
            header('Location: ' . BASE . '/' . $urlGet . '/atividades/corrigir-atividades');
        }
    }
    ?>
    <div id="activities">
        <div id="code">
            <?php
            $language = getLanguage($list['type']);
            ?>
            <a title="Baixar Exercício" class="float" href="<?= BASE . '/' . $urlGet ?>/atividades/baixar/<?= urlByIndex(4) ?>" target="_blank"><img alt="Baixar Exercício" src="<?= BASE ?>/tpl/images/down.png"/></a>
            <?php
            if ($language['readable'] == 1) {
                ?>
                <a title="Copiar Exercício" class="float" href="#copiar"><img alt="Copiar Exercício" src="<?= BASE ?>/tpl/images/copy.png"/><span class="msgCopy">Copiado!</span></a><small><a class="float" href="http://www.tutorialspoint.com/execute_<?= $language['hl_ref'] ?>_online.php" title="Compliador Online" target="_blank">(Compliador Online)</a></small>
                <?php
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
    <small style="color: #b00">Para uma melhor indentação, execute está tarefa no Google Chrome ou faça o download do arquivo.</small>
    <form name="formCorr" class="simple-form" method="POST" action="">
        <?php
        $readNotes = read('act_sent', 'WHERE order_id=? AND notes IS NOT NULL', [$sent['order_id']]);
        if ($readNotes) {
            $notes = array();
            foreach ($readNotes as $note) {
                $notes[] = $note['notes'];
            }
            $notes = array_unique($notes);
            $notes = array_splice($notes, 0, 7);
            echo '<label>';
            foreach ($notes as $note) {
                echo "<small class=\"p-notes\">" . resText($note, 50);
                echo "<input class=\"p-notes-text\" type=\"hidden\" id=\"pNote\" name=\"pNote\" value=\"" . htmlspecialchars($note) . "\">";
                echo "</small>";
            }
            echo '</label>';
        }
        ?>
        <label>
            <span>Notas sobre a correção</span>
            <?php
            $inputNote = "<textarea rows=\"5\" name=\"notes\" class=\"max-char\" maxlength=\"250\"";
            if (empty($expired)) {
                $inputNote .= ' disabled';
            }
            $inputNote .= " placeholder=\"Algum detalhe sobre a correção. (Opcional)\"/>";
            echo $inputNote;
            if (!empty($sent['notes'])) {
                echo $sent['notes'];
            } elseif (!empty($f['notes'])) {
                echo $f['notes'];
            }

            echo "</textarea>";
            ?>
            <small class="count-char">Restantes: 250</small>
        </label>
        <label>
            <span>Nota</span>
            <?php
            $inputGrade = "<input type=\"text\" name=\"grade\"";
            if (validFloat($sent['grade'])) {
                $inputGrade .= " value=\"{$sent['grade']}\"";
            } elseif (!empty($f['grade'])) {
                $inputGrade .= " value=\"{$f['grade']}\"";
            }
            if (!$expired) {
                $inputGrade .= ' disabled';
            }
            $inputGrade .= " placeholder=\"Digite a nota. Ex: 8.5\"/>";
            echo $inputGrade;
            ?>
        </label>
        <?php
        if (!$expired) {
            msg('A correção do exercício só pode ser registrada depois de sua data limite de envio!', 'alert');
        } else {
            echo '<input type="submit" class="btnBlue" name="sendForm" value="Corrigir"/>';
        }
        ?>
    </form>
    <?php
}
?>
<div id="matlab-copy" style="position: absolute; top: -1000px; left: -1000px; opacity: 0">
    <?= preg_replace("/(\s+)style=\"(.*?)\"/", '', strip_tags($geshi->parse_code(), '<pre><div>')); ?>
</div>