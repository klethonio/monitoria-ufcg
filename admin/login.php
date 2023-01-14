<?php
ob_start();
session_start();
require('../dts/dbaSis.php');
require('../dts/othSis.php');

if ($_SESSION['adUser']) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Painel Administrativo - <?= SITENAME ?></title>

        <meta name="title" content="Painel Administrativo - <?= SITENAME ?>" />
        <meta name="description" content="Área restrita aos administradores do site <?= SITENAME ?>" />
        <meta name="keywords" content="Login, Recuperar Senha, <?= SITENAME ?>" />

        <meta name="author" content="Klethônio Ferreira" />   
        <meta name="url" content="<?= BASE ?>/admin/login.php" />

        <meta name="language" content="pt-br" /> 
        <meta name="robots" content="NOINDEX,NOFOLLOW" /> 

        <link rel="icon" type="image/png" href="ico/chave.png" />
        <link rel="stylesheet" type="text/css" href="css/login.css" />
        <link rel="stylesheet" type="text/css" href="css/geral.css" />

    </head>

    <body>
        <div id="login">
            <div class="login-logo">
                <div class="band"></div>
                <h2>Turmas - UFCG</h2>
            </div>
            <?php
            $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            
            if (!empty($post['sendLogin'])) {
                unset($post['sendLogin']);

                if (!empty($post['login']) && !empty($post['pswd'])) {
                    if ($readAdmin = read('act_admin', 'WHERE login=? OR email=?', [$post['login'], $post['login']])) {
                        $admin = $readAdmin[0];
                        if (encPswd($post['pswd']) == $admin['pswd']) {

                            $_SESSION['adUser'] = $admin;
                            setcookie('adUser', 'true', time() + 60 * 15, '/');
                            header('Location: ' . BASE . '/admin');
                        } else
                            echo '<span class="ms no">E-mail ou senha incorretos!</span>';
                    } else
                        echo '<span class="ms no">E-mail ou senha incorretos!</span>';
                } else
                    echo '<span class="ms al">Preencha todos os campos!</span>';
            }
            ?>
            <?php
            if (empty($_GET['remember'])) {
                ?>
                <form name="login" action="" method="post">
                    <label>
                        <span>Login:</span>
                        <input type="text" class="radius" name="login" value="<?= $f['login'] ?? null ?>"/>
                    </label>
                    <label>
                        <span>Senha:</span>
                        <input type="password" class="radius" name="pswd" value="<?= $f['pswd'] ?? null ?>"/>
                    </label>
                    <input type="submit" value="Logar-se" name="sendLogin" class="btn" />

                    <a href="login.php?remember=true" class="link">Esqueci minha senha!</a>
                </form>
                <?php
            } else {
                if (!empty($post['sendRecover'])) {
                    $readAdmin = read('act_admin');
                    $admin = $readAdmin[0];
                    $recover = ($post['email']);
                    $readUser = read('cuc_users', 'WHERE email=?', [$recover]);
                    if ($readUser) {
                        foreach ($readUser as $user)
                            ;
                        if ($user['level'] == 1 || $user['level'] == 2) {
                            $key = sha1(uniqid(mt_rand(), true));
                            $readRec = read('cuc_recover', 'WHERE email=?', [$recover]);
                            if (update('cuc_users', ['code' => $key], 'WHERE email=?', [$recover])) {
                                $msg = '<div style="font:\'Trebuchet MS\', Arial, Helvetica, sans-serif;">';
                                $msg .= '<h3 style="color:#099;">Presado ' . $user['name'] . ', recupere seu acesso!</h3>';
                                $msg .= '<p style="color:#666">Estamos entrando em contato pois foi solicitado em nosso nível administrativo / editor a recuperação de dados de acesso. Para concluir o processo, caso essa operação tenha sido efetuada por você, clique no link abaixo!</p><hr/>';
                                $msg .= '<p style="color:#069"><em><a href="' . BASE . '/admin/recover.php?key=' . $key . '">CLIQUE AQUI</a></em></p><hr/>';
                                $msg .= '<h3 style="color:#900;">Atenciosamente, <strong>' . SITENAME . '</strong></h3>';
                                $msg .= '<p style="color:#666; font-size:12px;">enviada em: ' . date('d/m/Y H:i:s') . '</p></div>';
                                
                                if (sendMail('Recupere seus dados', $msg, $admin['pswd_gmail'], $admin['gmail'], SITENAME, $user['email'], $user['name'])) {
                                    echo '<span class="ms ok">Um e-mail foi enviado para <strong>' . $recover . '</strong> com instruções para o resgate da senha. Favor, verifque caixa de spam!</span>';
                                } else {
                                    echo '<span class="ms no">Erro: Operação não realizada, entre em contato com nossa equipe!</span>';
                                }
                            }
                        } else {
                            echo '<span class="ms al">Nível de usuário não permitido! Será redirecionado</span>';
                            header('Refresh: 3; url="' . BASE . '/pagina/login"');
                        }
                    } else {
                        echo '<span class="ms no">Error: E-mail não confere!</span>';
                    }
                }
                ?>
                <form name="recover" action="" method="post">
                    <span class="ms in">Informe seu e-mail para que possamos enviar seus dados de acesso!</span>
                    <label>
                        <span>E-mail:</span>
                        <input type="text" class="radius" name="email" value="<?= $recover ?>" />
                    </label>
                    <input type="submit" value="Recuperar dados" name="sendRecover" class="btn" />
                    <a href="login.php" class="link">Voltar</a>
                </form>
                <?php
            }
            ?>
        </div><!-- //login -->

    </body>
    <?php ob_end_flush(); ?>
</html>