<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 2) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor!', 'alert');
} else {
    ?>
    <div id="lists">
        <h1>Adicionar Lista de Exercícios</h1>
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            $f['num'] = $post['numList'];
            $f['num_exe'] = $post['num_exe'];
            $f['description'] = $post['description'];
            $f['type'] = $post['type'];
            if (in_array('', $f)) {
                msg('Erro: Preencha todos os campos necessários', 'error');
            } elseif (!validFloat($f['num'], 1) || !validInt($f['num_exe'])) {
                msg('Erro: Campo Lista ou Número de Exercícios não contém um valor válido!', 'error');
            } elseif (!$_FILES['file']['name'] && !$post['url']) {
                msg('Erro: Você deve enviar uma lista ou informar uma url.', 'error');
            } elseif (!is_numeric(getLanguage($f['type'], 'id'))) {
                msg('Erro: Tipo de lista inválida.', 'error');
            } else {
                $f['class_id'] = $class['id'];
                $file = $_FILES['file'];
                if (read('act_lists', 'WHERE class_id=? AND num=?', [$class['id'], $f['num']])) {
                    msg('Erro: Lista já cadastrada!', 'error');
                } else {
                    $noFile = FALSE;
                    $stop = FALSE;
                    if ($file['name']) {
                        preg_match('/\.(pdf)$/i', $file['name'], $ext);
                        if ($file['size'] > 5242880) {
                            msg('Erro: O tamanho da lista não deve ultrapassar 5MB!', 'error');
                            $stop = TRUE;
                        } elseif ($ext[1] != 'pdf') {
                            msg('Erro: Extensão da lista deve ser .pdf!', 'error');
                            $stop = TRUE;
                        } else {
                            $listName = 'Ref-' . $class['id'] . '_Lista_' . $f['num'] . '.pdf';
                            if (!is_dir('./listas')) {
                                mkdir('./listas');
                            }
                            if (!move_uploaded_file($file['tmp_name'], './listas/' . $listName)) {
                                $noFile = TRUE;
                            } else {
                                $f['file'] = $listName;
                            }
                        }
                    } else {
                        $f['url'] = $post['url'];
                        if (!validUrl($f['url'])) {
                            msg('Erro: Url inválida, veja as especificações!', 'error');
                            $stop = TRUE;
                        }
                    }
                    if (!$stop) {
                        if (create('act_lists', $f)) {
                            if ($noFile) {
                                $_SESSION['success'] = 'Lista criada com sucesso!<b> Porém a lista não foi enviada.</b>';
                            } else {
                                $_SESSION['success'] = 'Lista criada com sucesso!';
                            }
                            header('Location: ' . BASE . '/' . $urlGet . '/listas/gerenciar');
                        }
                    }
                }
            }
        }
        ?>
        <form name="formList" class="simple-form" method="POST" action="" enctype="multipart/form-data">
            <label>
                <span>Número da Lista</span>
                <input type="text" name="numList" value="<?= ($post['num'] ?? null) ?>" placeholder="Referência da Lista"/>
                <small class="obs">Ex.: 1; 1.2; 2.2; 3 etc.</small>
            </label>
            <label>
                <span>Número(qnt.) de Exercícios</span>
                <input type="text" name="num_exe" value="<?= ($post['num_exe'] ?? null) ?>" placeholder="Quantidade de Exercícios"/>
            </label>
            <label>
                <span>Descrição da Lista</span>
                <input type="text" name="description" value="<?= ($post['description'] ?? null) ?>" placeholder="Sobre o que a lista trata"/>
            </label>
            <label>
                <span>Tipo da lista</span>
                <select name="type">
                    <?php
                    $types = read('act_languages');
                    foreach ($types as $type) {
                        if(($post['type'] ?? null) == $type['id']) {
                            echo '<option value="'.$type['id'].'" selected>'.$type['name'].'</option>';
                        }else{
                            echo '<option value="'.$type['id'].'">'.$type['name'].'</option>';
                        }
                    }
                    ?>
                </select>
                <small class="obs">Como o exercício será enviado.</small>
            </label>
            <label>
                <span>Link para download da Lista</span>
                <input type="text" class="inputUrl" name="url" value="<?= ($post['url'] ?? null) ?>" placeholder="Com http://. Ex: http://www.google.com"/>
                <small class="obs">Escolher entre link externo ou enviar o pdf da lista abaixo.</small>
            </label>
            <label>
                <span>Enviar lista para servidor</span>
                <input type="file" class="inputUrl" name="file"/>
                <small class="obs">Apenas arquivos .pdf</small>
            </label>
            <input type="submit" name="sendForm" class="btnBlue" value="Criar Lista"/>
        </form>
    </div>
    <?php
}
?>