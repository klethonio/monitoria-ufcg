<?php
/* @author Klethônio Ferreira */
global $actUser, $setcookie, $url, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
if (!checkUser($actUser['id'], 2) || !$setcookie) {
    msg('Para acessar essa página você precisa ser um professor!', 'alert');
} else {
    if (!$readList = read('act_lists', 'WHERE class_id=? AND id=?', [$class['id'], urlByIndex(4)])) {
        header('Location: ' . BASE . '/' . $urlGet . '/listas/gerenciar');
    } else {
        $list = $readList[0];
    }
    ?>
    <div id="lists">
        <h1>Adicionar Lista de Exercícios - Lista <?= $list['num'] ?></h1>
        <?php
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['sendForm'])) {
            $f['num_exe'] = $post['num_exe'];
            $f['description'] = $post['description'];
            if (in_array('', $f)) {
                msg('Erro: Preencha todos os campos necessários', 'error');
            } elseif (!validInt($f['num_exe'])) {
                msg('Erro: Número de Exercícios não contém um valor inteiro!', 'error');
            } elseif (!$_FILES['fileList']['name'] && !$post['url'] && empty($list['file']) && empty($list['url'])) {
                msg('Erro: Você deve enviar uma lista ou informar uma url.', 'error');
            } else {
                $fileList = $_FILES['fileList'];
                $noFile = FALSE;
                $stop = FALSE;
                $f['num_exe'] = $f['num_exe'] > $list['num_exe'] ? $f['num_exe'] : $list['num_exe'];
                $f['url'] = $list['url'];
                $f['file'] = $list['file'];
                if ($fileList['name']) {
                    $f['url'] = NULL;
                    preg_match('/\.(pdf)$/i', $fileList['name'], $ext);
                    if ($ext[1] != 'pdf') {
                        msg('Error: Extensão da lista deve ser .pdf!', 'error');
                        $stop = TRUE;
                    } else {
                        $listName = 'Ref-' . $class['id'] . '_Lista_' . $list['num'] . '.pdf';
                        if (!is_dir('./listas')) {
                            mkdir('./listas');
                        }
                        if (!move_uploaded_file($fileList['tmp_name'], './listas/' . $listName)) {
                            $noFile = TRUE;
                        } else {
                            $f['file'] = $listName;
                        }
                    }
                } else {
                    if (!validUrl($post['url']) && !empty($post['url'])) {
                        msg('Erro: Url inválida, veja as especificações!', 'error');
                        $stop = TRUE;
                    } elseif ($post['url']) {
                        $f['file'] = NULL;
                        $f['url'] = $post['url'];
                        if (file_exists('./listas/Ref-' . $class['id'] . '_Lista_' . $list['num'] . '.pdf')) {
                            unlink('./listas/Ref-' . $class['id'] . '_Lista_' . $list['num'] . '.pdf');
                        }
                    }
                }
                if (!$stop) {
                    update('act_lists', $f, 'WHERE class_id=? AND id=?', [$class['id'], urlByIndex(4)]);
                    if ($noFile) {
                        $_SESSION['success'] = 'Lista editada com sucesso!<b> Porém a lista não foi enviada.</b>';
                    } else {
                        $_SESSION['success'] = 'Lista editada com sucesso!';
                    }
                    header('Location: ' . BASE . '/' . $urlGet . '/listas/gerenciar');
                }
            }
        }
        ?>
        <form name="formList" class="simple-form" method="POST" action="" enctype="multipart/form-data">
            <label>
                <span>Número(qnt.) de Exercícios</span>
                <input type="text" name="num_exe" value="<?= $list['num_exe'] ?>" placeholder="Quantidade de Exercícios"/>
                <small class="obs">O número de exercícios não pode ser reduzido.</small>
            </label>
            <label>
                <span>Descrição da Lista</span>
                <input type="text" name="description" value="<?= $list['description'] ?>" placeholder="Sobre o que a lista se trata"/>
            </label>

            <label>
                <span>Tipo da lista</span>
                <input type="text" name="type" readonly disabled value="<?= getLanguage($list['type'], 'name') ?>"/>
                <small class="obs">Como o exercício será enviado.</small>
            </label>
            <label>
                <span>Link para download da Lista</span>
                <input type="text" class="inputUrl" name="url" value="<?= $list['url'] ?>" placeholder="Com http://. Ex: http://www.google.com"/>
            </label>
            <label>
                <span>Enviar lista para servidor</span>
                <input type="file" class="inputUrl" name="fileList"/>
                <small class="obs">Apenas arquivos .pdf</small>
            </label>
            <input type="submit" name="sendForm" class="btnBlue" value="Editar Lista"/>
        </form>
    </div>
    <?php
}
?>