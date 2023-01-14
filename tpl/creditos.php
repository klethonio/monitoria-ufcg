<?php
global $urlGet;
?>
<style type="text/css">
    #credits b{
        color: #428bca;
        position: relative;
        cursor: pointer;
    }
    #credits b:hover{
    color: rgba(35, 82, 124, 1);
    text-decoration: underline;
}
    #credits b span {
        display: none;
        margin-top:7px;
        position: absolute;
        left: -5px;
        padding: 5px;
        color: #fff;
        background:#428bca;
        opacity: .8;
        font-size: 7pt;
        border:1px solid #428bca;
        border-radius: 4px;
        border-top-left-radius: 0;
    }
    #credits b:hover span {
        display: block;
    }
    #credits b:hover span:hover {
        display: none;
    }
    #credits b span:before {
        content:'';
        position:absolute;
        left:-1px;
        top:-7px;
        border-left:7px solid transparent;
        border-right:7px solid transparent;
        border-bottom:7px solid #428bca
    }
</style>
<div id="credits">
    <h1>Créditos</h1>
    <?= msg('Estrutura e códigos  CSS, javascript e PHP desenvolvidos por <b>Klethônio Ferreira<span>klethonio_@hotmail.com</span></b> em 03/2016.', 'infor'); ?>
    <a href="<?= BASE . '/' . $urlGet ?>">Voltar</a>
</div>