<?php
if (!function_exists('getUser')) {
    header('Location: ../');
}
?>
<div class="bloco form" style="display:block">
    <div class="titulo">Abrir turma: <a href="?url=turmas/gerenciar" class="btnalt" style="float:right;">Voltar</a></div>
    <?php
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($post['sendForm'])) {
        unset($post['sendForm']);
        $year = date('Y');
        $f_class['period'] = $post['year'].'.'.$post['half'];
        $f_class['class'] = intval($post['class']);
        $f_class['discipline_id'] = $post['discipline'];
        $f_prof['name'] = uppercaseFirst(trim($post['name']));
        $f_prof['register'] = $post['register'];
        $f_prof['email'] = $post['mail'];
        $f_prof['pswd'] = encPswd($post['pswd']);
        $f_prof['born_date'] = date('Y-m-d');
        $rePswd = $post['rePswd'];
        if (in_array('', $f_prof) || in_array('', $f_class) || empty($rePswd)) {
            echo '<span class="ms al">Preencha todos os campos!</span>';
        } else {
            $readClass = read('act_config_class', 'WHERE period=? AND class=? AND discipline_id=?', [$f_class['period'], $f_class['class'], $f_class['discipline_id']]);
            $readDiscipline = read('act_disciplines', 'WHERE id=?', [$f_class['discipline_id']]);

            if ($readClass) {
                echo '<span class="ms al">Erro: Turma já cadastrada!</span>';
            } elseif (!$readDiscipline) {
                echo '<span class="ms al">Erro: Disciplina não encontrada!</span>';
            } elseif ($post['year'] < ($year-1) || $post['year'] > ($year+1)) {
                echo '<span class="ms al">Erro: Ano seleciondo indevidamente!</span>';
            } elseif (!preg_match('/^20[1-9]{2}\.[1|2]$/', $f_class['period'])) {
                echo '<span class="ms al">Erro: Período digitado é inválido!</span>';
            } elseif (!preg_match('/^[1]?[1-9]$/i', $f_class['class'])) {
                echo '<span class="ms al">Erro: Turma digitada inválida ou possui mais de 2 números!</span>';
            } elseif (!validInt($f_prof['register']) || strlen($f_prof['register']) < 5) {
                echo '<span class="ms al">Erro: Matrícula digitada inválida ou possui menos de 5 caracteres!</span>';
            } elseif (!validMail($f_prof['email'])) {
                echo '<span class="ms al">Erro: E-mail digitado é inválido!</span>';
            } elseif ($post['pswd'] != $rePswd) {
                echo '<span class="ms al">Erro: As senhas digitadas não conferem!</span>';
            } else {
                $f_prof['level'] = 2;
                $f_prof['status'] = 1;
                $f_prof['class_id'] = 0;
                $f_prof['course'] = 'Professor';
                if ($profId = create('act_users', $f_prof)) {
                    $_SESSION['return'] = '<span class="ms ok">Professor adicinado com sucesso!</span>';
                    $f_class['professor_id'] = $profId;
                    $f_class['status'] = 1;
                    if ($classId = create('act_config_class', $f_class)) {
                        $_SESSION['return'] .= '<span class="ms ok">Turma adicinada com sucesso!</span>';
                        if (update('act_users', ['class_id' => $classId], 'WHERE id=?', [$profId])) {
                            $_SESSION['return'] .= '<span class="ms ok">Professor atribuido a turma. Operação concluída!</span>';
                        } else {
                            delete('act_config_class', 'WHERE id=?', [$classId]);
                            delete('act_users', 'WHERE id=?', [$profId]);
                            $_SESSION['return'] .= '<span class="ms no">Professor não adicionado a turma!</span>';
                            $_SESSION['return'] .= '<span class="ms al">Professor deletado!</span>';
                            $_SESSION['return'] .= '<span class="ms al">Turma deletada!</span>';
                        }
                    } else {
                        delete('act_users', 'WHERE id=?', [$profId]);
                        $_SESSION['return'] .= '<span class="ms no">Erro ao adicionar turma!</span>';
                        $_SESSION['return'] .= '<span class="ms al">Professor deletado!</span>';
                    }
                    header('Location: ?url=turmas/gerenciar');
                } else {
                    echo '<span class="ms no">Erro ao adicionar professor!</span>';
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
                    $year = date('Y');
                    for ($i = -1; $i <= 1; $i++) {
                        echo '<option value="' . ($year + $i) . '">'.($year + $i).'</option>';
                    }
                    ?>
                </select>
            </label>
            <label>
                <select class="input-half" name="half">
                    <option disabled selected value="">Semestre</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </label>
            <small class="obs">Ex.: 2016.1; 2016.2 etc.</small>
        </div>
        <label class="line">
            <span class="data">Disciplina:</span>
            <select class="input-pattern" name="discipline">
                <option disabled selected value="">Selecione a disciplina</option>
                <?php
                $readDisciplines = read('act_disciplines');
                foreach ($readDisciplines as $disciplines) {
                    echo '<option' . (($f_class['discipline_id'] ?? null) == $disciplines['id'] ? ' selected' : '') . ' value="' . $disciplines['id'] . '">' . $disciplines['name'] . '</option>';
                }
                ?>
            </select>
        </label>
        <label class="line">
            <span class="data">Turma:</span>
            <input class="input-pattern" type="text" name="class" value="<?= $f_class['class'] ?? null ?>" />
            <small class="obs">O número da turma no período e não o ID.</small>
        </label>
        <div class="titulo">Dados do Professor:</div>
        <label class="line">
            <span class="data">Nome:</span>
            <input class="input-pattern" type="text" name="name" value="<?= $f_prof['name'] ?? null ?>" />
        </label>
        <label class="line">
            <span class="data">Matrícula:</span>
            <input class="input-pattern" type="text" name="register" value="<?= $f_prof['register'] ?? null ?>" />
            <small class="obs">Pode ser 12345678, por exemplo. Mínimo de 5 caracters.</small>
        </label>
        <label class="line">
            <span class="data">E-mail:</span>
            <input class="input-pattern" type="text" name="mail" value="<?= $f_prof['email'] ?? null ?>" />
        </label>
        <label class="line">
            <span class="data">Senha:</span>
            <input class="input-pattern" type="password" name="pswd" />
        </label>
        <label class="line">
            <span class="data">Repetir Senha:</span>
            <input class="input-pattern" type="password" name="rePswd" />
        </label>
        <input type="reset" value="Limpar" class="btnalt" />
        <input type="submit" value="Criar" name="sendForm" class="btn" />
    </form>

</div><!-- /bloco form -->