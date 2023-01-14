<?php
/* @author Klethônio Ferreira */
require('dts/dbaSis.php');
require('dts/othSis.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="title" content="Atividades UFCG" />
        <meta name="description" content="<?= SITEDESC ?>" />
        <meta name="keywords" content="<?= SITETAGS ?>" />
        <meta name="author" content="Klethônio Ferreira" />   
        <meta name="url" content="<?= BASE ?>" />  
        <meta name="language" content="pt-br" /> 
        <meta name="robots" content="INDEX,FOLLOW" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link type="image/x-icon" href="<?= BASE ?>/tpl/images/favicon.ico" rel="shortcut icon">
        <link rel="stylesheet" href="<?= BASE ?>/tpl/css/style.css"/>
        <title>Atividades UFCG</title>
    </head>
    <body>
        <div id="site">
            <header>
                <a href="<?= BASE ?>" title="Atividades UFCG">
                    <h1>Turmas - Atividades UFCG</h1>
                </a>
            </header>
            <section id="content">
                <?php
                msg('Seu navegador não tem suporte para javascript ou está desativado, ative-o.', 'error');
                ?>
            </section>
            <?php include 'tpl/footer.php'; ?>
        </div>
        <!--Desenvolvido por Klethônio Ferreira-->
    </body>
</html>