<?php
ob_start();
session_start();
require('../dts/dbaSis.php');
require('../dts/getSis.php');
require('../dts/othSis.php');

expiredClasses();

if (!empty($_SESSION['adUser'])) {
    $readAdmin = read('act_admin', 'WHERE login=?', [$_SESSION['adUser']['login']]);
    if ($readAdmin) {
        $adUser = $readAdmin[0];
        if ($adUser['pswd'] != $_SESSION['adUser']['pswd'] || !$_COOKIE['adUser']) {
            unset($_SESSION['adUser']);
            setcookie('adUser', "", time() - 3600);
            header('Location: index.php');
        } else {
            setcookie('adUser', 'true', time() + 60 * 15, '/');
        }
    } else {
        unset($_SESSION['adUser']);
        header('Location: ' . BASE . '/admim/login.php');
    }
} else
    header('Location: ' . BASE . '/admin/login.php');
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title>Painel Administrativo - <?= SITENAME ?></title>

    <meta name="title" content="Painel Administrativo - <?= SITENAME ?>" />
    <meta name="description" content="Área restrita aos administradores do site <?= SITENAME ?>" />
    <meta name="keywords" content="Login, Recuperar Senha, <?= SITENAME ?>" />

    <meta name="author" content="Klethonio Ferreira" />   
    <meta name="url" content="<?= BASE ?>/admin/index.php" />

    <meta name="language" content="pt-br" /> 
    <meta name="robots" content="NOINDEX,NOFOLLOW" />

    <link rel="icon" type="image/png" href="ico/chave.png" />
    <link rel="stylesheet" type="text/css" href="css/painel.css" />
    <link rel="stylesheet" type="text/css" href="css/geral.css" />

</head>
<body>
    <div id="band"></div>
    <div id="painel">
        <?php require('includes/header.php'); ?>
        <div id="content">
            <?php require('includes/menu.php'); ?>
            <div class="pg">
                <?php
                $urlGet = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);

                if ($urlGet) {
                    $url = $urlGet;
                    $url = explode('/', $url);
                    $url[0] = !urlByIndex(0) ? 'home.php' : urlByIndex(0);
                    if (file_exists(urlByIndex(0) . '.php')) {
                        require(urlByIndex(0) . '.php');
                    } elseif (file_exists(urlByIndex(0) . '/' . urlByIndex(1) . '.php')) {
                        require(urlByIndex(0) . '/' . urlByIndex(1) . '.php');
                    } else
                        echo '<span class="ms in">Página não encontrada!</span>';
                }else {
                    require('home.php');
                }
                ?>
            </div><!-- pg -->
        </div><!-- /content -->
        <div style="clear:both"></div> 
        <div id="footer">Desenvolvido por <b>Klethônio Ferreira<span>klethonio_@hotmail.com</span></b></div> <!-- //footer -->
    </div><!-- //painel -->
</body>
<?php
include('../js/jsSis.php');
ob_end_flush();
?>
</html>