<?php
if (!function_exists('getUser')) {
    header('Location: ../');
}
$classId = $_GET['classid'];
if ($readClass = read('act_config_class', 'WHERE id=?', [$classId])) {
    $class = $readClass[0];

    $year = date('Y');
    $periodClass = explode('.', $class['period']);
    $yearClass = $periodClass[0];
    $halfClass = $periodClass[1];
} else {
    header('Location: ?url=turmas/gerenciar');
}
?>
<div class="bloco form" style="display:block;">
    <div class="titulo">Editar turma <span style="color: #900;"><?= addZero($class['class']) ?></span> - <?= strtoupper(getDiscipline($class['discipline_id'], 'url')) ?>: <a href="?url=turmas/gerenciar" class="btnalt" style="float:right;">Voltar</a></div>
    <?php
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendForm'])) {
        if ($post['sendForm'] == 'Editar Turma') {
            $f['class'] = $post['class'];
            $f['period'] = $post['year'] . '.' . $post['half'];
    
            $yearArray = array();
            for ($i = -1; $i <= 1; $i++) {
                $yearArray[] = $year + $i;
            }
    
            if (in_array('', $f)) {
                echo '<span class="ms al">Preencha todos os campos necessários!</span>';
            } elseif (!in_array($yearClass, $yearArray)) {
                echo '<span class="ms no">Erro: A turma é muito antiga para que o ano possa ser editada!</span>';
            } elseif ($post['year'] < ($year - 1) || $post['year'] > ($year + 1)) {
                echo '<span class="ms al">Erro: Ano seleciondo indevidamente!</span>';
            } elseif (!preg_match('/^20[1-9]{2}\.[1|2]$/', $f['period'])) {
                echo '<span class="ms no">Erro: Período digitado é inválido!</span>';
            } elseif (!preg_match('/^[1]?[1-9]$/i', $f['class'])) {
                echo '<span class="ms no">Erro: Turma digitada inválida ou possui mais de 2 números!</span>';
            } else {
                if ($readClasses = read('act_config_class', 'WHERE class=? AND period=? AND discipline_id=? AND id<>?', [$f['class'], $f['period'], $class['discipline_id'], $class['id']])) {
                    echo '<span class="ms no">Erro: Turma já cadastrada!</span>';
                } else {
                    update('act_config_class', $f, 'WHERE id=?', [$class['id']]);
                    $_SESSION['return'] = '<span class="ms ok">Dados da turma editados com sucesso!</span>';
                    header('Location: ?url=turmas/gerenciar');
                }
            }
        }
    }
    ?>
    <form name="formulario" action="" method="post">
        <div class="line">
            <span class="data">Período:</span>
            <label>
                <select class="input-half" name="year">
                    <option disabled selected value="">Ano</option>
                    <?php
                    for ($i = -1; $i <= 1; $i++) {
                        echo '<option';
                        if ($yearClass == ($year + $i)) {
                            echo ' selected';
                        }
                        echo ' value="' . ($year + $i) . '">' . ($year + $i) . '</option>';
                    }
                    ?>
                </select>
            </label>
            <label>
                <select class="input-half" name="half">
                    <option disabled selected value="">Semestre</option>
                    <option <?php if($halfClass == 1){echo'selected';} ?> value="1">1</option>
                    <option <?php if($halfClass == 2){echo'selected';} ?> value="2">2</option>
                </select>
            </label>
            <small class="obs">Ex.: 2016.1; 2016.2 etc.</small>
        </div>
        <label class="line">
            <span class="data">Turma:</span>
            <input class="input-pattern" type="text" name="class" value="<?= $class['class'] ?>" />
            <small class="obs">O número da turma no período e não o ID.</small>
        </label>
        <input type="submit" value="Editar Turma" name="sendForm" class="btn" />
    </form>
</div>
<div class="bloco form" style="display:block">
    <div class="titulo">Editar dados do professor:</div>
    <form name="formulario" action="" method="post">
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            if ($post['sendForm'] == 'Editar Senha') {
                $f['pswd'] = encPswd($post['pswd']);
                $rePswd = $post['rePswd'];
                if (in_array('', $f) || empty($rePswd)) {
                    echo '<span class="ms al">Preencha todos os campos necessários!</span>';
                } elseif ($post['pswd'] != $rePswd) {
                    echo '<span class="ms no">Erro: As senhas digitadas não conferem!</span>';
                } else {
                    update('act_users', $f, 'WHERE id=?', [$class['professor_id']]);
                    $_SESSION['return'] = '<span class="ms ok">Dados do professor editados com sucesso!</span>';
                    header('Location: ?url=turmas/gerenciar');
                }
                echo '<script>document.addEventListener("DOMContentLoaded", function() { document.getElementById("btn-edit-professor").focus(); });</script>';
            }
        }
        $prof = getUser($class['professor_id']);
        ?>
        <label class="line">
            <span class="data">Nome:</span>
            <input class="input-pattern" type="text" disabled readonly name="register" value="<?= $prof['name'] ?>" />
        </label>
        <label class="line">
            <span class="data">E-mail:</span>
            <input class="input-pattern" type="text" disabled readonly name="register" value="<?= $prof['email'] ?>" />
        </label>
        <label class="line">
            <span class="data">Matrícula:</span>
            <input class="input-pattern" type="text" disabled readonly name="register" value="<?= $prof['register'] ?>" />
        </label>
        <label class="line">
            <span class="data">Nova Senha:</span>
            <input class="input-pattern" type="password" name="pswd" />
        </label>
        <label class="line">
            <span class="data">Repetir Senha:</span>
            <input class="input-pattern" type="password" name="rePswd" />
        </label>
        <input type="submit" id="btn-edit-professor" value="Editar Senha" name="sendForm" class="btn" />
    </form>

</div><!-- /bloco form -->