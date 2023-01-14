<?php

/* @author Klethônio Ferreira */
ob_start();
session_start();
unset($_SESSION['actUser']);
setcookie('actUser', "", time() - 3600);
header('Location: ./');
ob_end_flush();