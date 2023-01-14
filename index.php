<?php
/* @author Klethônio Ferreira */
ob_start();
session_start();
require('dts/dbaSis.php');
require('dts/getSis.php');
require('dts/othSis.php');
require('dts/geshi.php');

expiredClasses();

$urlGet = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);

$test_class = preg_match('/^20[1-9]{2}\.[1|2]\/(' . returnDisciplines() . ')\-[1]?[1-9]/i', $urlGet, $discipline);

if (!empty($_GET['url']) && !$test_class && $_GET['url'] != 'creditos') {
    header('Location: ' . BASE);
} elseif ($test_class) {

    $url = explode('/', $urlGet);
    $period = urlByIndex(0);
    $numClass = explode('-', urlByIndex(1));
    $numClass = $numClass[1];
    $urlGet = $period . '/' . urlByIndex(1);
    $discipline = $discipline[1];
    
    $stopHeader = array('login', 'creditos', 'cadastro');

    $readClass = read('act_config_class', 'JOIN act_disciplines AS d ON a.discipline_id = d.id WHERE a.period=? AND a.class=? AND d.url=? AND a.status=1', [$period, $numClass, $discipline]);
    $class = $readClass[0];
    $readConfig = read('act_admin');
    $config['gmail'] = $readConfig[0]['gmail'];
    $config['pswd_gmail'] = $readConfig[0]['pswd_gmail'];
    $config['max_size_files'] = $readConfig[0]['max_size_files'];

    if (empty($class)) {
        header('Location: ' . BASE);
    } elseif (empty($_SESSION['actUser']) && !in_array(urlByIndex(2), $stopHeader)) {
        header('Location: ' . BASE . '/' . $urlGet . '/login');
    } elseif (!empty($_SESSION['actUser']) && (urlByIndex(2) == 'login' || urlByIndex(2) == 'cadastro')) {
        header('Location: ' . BASE . '/' . $urlGet . '/home');
    } elseif (!empty($_SESSION['actUser'])) {
        $loginTrue = FALSE;
        if ($readUser = read('act_users', 'WHERE id=? AND class_id=?', [$_SESSION['actUser']['id'], $class['id']])) {
            $actUser = $readUser[0];
            if ($actUser['pswd'] == $_SESSION['actUser']['pswd']) {
                $loginTrue = TRUE;
            }
        }
        if (!$loginTrue || !filter_input(INPUT_COOKIE, 'actUser')) {
            unset($_SESSION['actUser']);
            setcookie('actUser', 'true', time() - 3600, '/');
            unset($_COOKIE['actUser']);
            setcookie('actUser', null, -1, '/');
            header('Location: ' . BASE . '/' . $urlGet . '/login');
        } else {
            setcookie('actUser', 'true', time() + 60 * 15, '/');
            $setcookie = TRUE;
        }
    }
}
if (empty(urlByIndex(0))) {
    $title = 'Turmas - ';
} else {
    $discipline = getDiscipline($class['discipline_id']);
    $title = 'Turma ' . addZero($numClass) . ' - ' . $period . ' - ';
    $title .=!empty(urlByIndex(2)) ? ucfirst(urlByIndex(2)) . ' - ' : 'Home - ';
    if (!empty(urlByIndex(3))) {
        $subTitle = explode('-', urlByIndex(3));
        if (count($subTitle) == 1) {
            $subTitle = ucfirst($subTitle[0]);
        } else {
            $subTitle = ucfirst($subTitle[0]) . ' ' . ucfirst($subTitle[1]);
        }
        $title .= $subTitle . ' - ';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <noscript><meta http-equiv="Refresh" content="1; url=http://localhost/monitoria_novo/nojavascript.php"/></noscript>
        <meta charset="UTF-8">
        <meta name="title" content="<?= $title . (!empty($discipline['name']) ? $discipline['name'] : 'Atividades UFCG') ?>" />
        <meta name="description" content="<?= SITEDESC ?>" />
        <meta name="keywords" content="<?= SITETAGS ?>" />
        <meta name="author" content="Klethônio Ferreira" />   
        <meta name="url" content="<?= BASE ?>" />  
        <meta name="language" content="pt-br" /> 
        <meta name="robots" content="INDEX,FOLLOW" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link type="image/x-icon" href="<?= BASE ?>/tpl/images/favicon.ico" rel="shortcut icon">
        <link rel="stylesheet" href="<?= BASE ?>/tpl/css/style.css"/>
        <title><?= $title . (!empty($discipline['name']) ? $discipline['name'] : 'Atividades UFCG') ?></title>
    </head>
    <body>
        <div id="site">
            <header>
                <a href="<?= BASE ?>" title="Atividades UFCG">
                    <h1>
                        <?php
                        if (empty($class)) {
                            echo 'Turmas - Atividades UFCG';
                        } else {
                            echo 'Turma ' . addZero($class['class']) . ' - ' . $class['period'] . ' - ' . $discipline['name'];
                        }
                        ?>
                    </h1>
                </a>
            </header>
            <?php
            if (!empty($_SESSION['actUser']) && $test_class) {
                include 'tpl/header.php';
            }
            ?>
            <section id="content">
                <?php
                if (!empty($_SESSION['actUser']) && $test_class) {
                    echo '<p class="count-down">Sua sessão expira em: 15:00</p>';
                }
                ?>
                <?php
                if ($urlGet == 'creditos') {
                    include 'tpl/creditos.php';
                } elseif (!$test_class) {
                    echo '<h1 id="h1-classes">Selecione sua turma para acessar o painel</h1>';
                    $readClasses = read('act_config_class', 'WHERE status=1');
                    if (empty($readClasses)) {
                        msg('Nenhuma turma cadastrada/ativada.', 'alert');
                    } else {
                        foreach ($readClasses as $class) {
                            $discipline = getDiscipline($class['discipline_id']);
                            $link = $class['period'] . '/' . $discipline['url'] . '-' . $class['class'];
                            echo '<div class="class"><a href="' . $link . '" title="' . $discipline['name'] . ' ' . addZero($class['class']) . ' - ' . $class['period'] . '">' . $discipline['name'] . ' ' . addZero($class['class']) . ' - ' . $class['period'] . '</a></div>';
                        }
                    }
                } else {
                    getUrl();
                }
                ?>
            </section>
            <?php include 'tpl/footer.php'; ?>
        </div>
        <!--Desenvolvido por Klethônio Ferreira-->
    </body>
    <?php
    include('js/jsSis.php');
    ob_end_flush();
    ?>
</html>