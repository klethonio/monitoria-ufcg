<?php

/* @author Klethônio Ferreira */
function dd(...$vars): void
{
    echo '<pre class="debug">';

    foreach ($vars as $var) {
        echo '<hr>';
        var_dump($var);
        echo '<hr>';
    }

    echo '</pre>';
    die;
}

/* * *********************************
  FUNÇÃO VALIDAR E-MAIL
 * ********************************* */

function urlByIndex($index) {
    global $url;
    return $url[$index] ?? null;
}

/* * *********************************
  FUNÇÃO VALIDAR E-MAIL
 * ********************************* */

function validMail($mail) {
    if (preg_match("/^[^0-9.\-][a-z0-9_\.\-]+@[a-z0-9]+[a-z0-9_\.\-]*[.][a-z]{2,4}$/", $mail)) {
        return true;
    }
}

/* * *********************************
  FUNÇÃO VALIDAR URL
 * ********************************* */

function validUrl($url) {
    if (preg_match("/^(http|https)\:\/\/[a-z0-9\.\-\/\_]*/i", $url)) {
        return true;
    }
}

/* * *********************************
  FUNÇÃO VALIDAR INT
 * ********************************* */

function validInt($int) {
    if (preg_match("/^[0-9]+$/", $int)) {
        return true;
    }
}

/* * *********************************
  FUNÇÃO VALIDAR FLOAT
 * ********************************* */

function validFloat($float, $decimal = 1) {
    if (preg_match("/^[0-9]{1,2}([\.\,][0-9]{" . $decimal . "})?$/", $float)) {
        return true;
    }
}

/* * *********************************
  FUNÇÃO DATA TIMESTAMP
 * ********************************* */

function formDate($date) {
    if (!empty($date)) {
        $result = date('Y-m-d 23:59:59', strtotime($date));
        return $result;
    }
    return $date;
}

/* * *********************************
  FUNÇÃO RESUMIR
 * ********************************* */

function resText($string, $words = 50) {
    $string = strip_tags($string);
    if (strlen($string) <= $words) {
        return $string;
    } else {
        $strrpos = strrpos(substr($string, 0, $words), ' ');
        return substr($string, 0, $strrpos) . '...';
    }
}

/* * *********************************
  FUNÇÃO PAGINAR
 * ********************************* */

function paginator($table, $max, $url, $pag, $cond = NULL, array $params = NULL, $width = NULL, $maxLinks = 4) {
    $readPag = read($table, $cond, $params);
    $totalData = count($readPag);
    if ($totalData > $max) {
        $totalPags = ceil($totalData / $max);
        if ($width) {
            echo '<div class="paginator" style="width:' . $width . '">';
        } else
            echo '<div class="paginator">';
        if ($pag > $maxLinks + 1)
            echo '<a href="' . $url . '1">Primeira</a> ';
        for ($i = $pag - $maxLinks; $i <= $pag - 1; $i++) {
            if ($i >= 1) {
                echo '<a href="' . $url . $i . '">' . $i . '</a> ';
            }
        }
        echo '<span class="curentPag">' . $pag . '</span> ';
        for ($i = $pag + 1; $i <= $pag + $maxLinks; $i++) {
            if ($i <= $totalPags) {
                echo '<a href="' . $url . $i . '">' . $i . '</a> ';
            }
        }
        if ($pag + $maxLinks < $totalPags)
            echo '<a href="' . $url . $totalPags . '">Última</a>';
        echo '</div><!-- paginator -->';
    }
}

/* * *********************************
  FUNÇÃO ENVIAR E-MAIL
 * ********************************* */

function sendMail($subject, $message, $pass, $from, $fromName, $to, $toName, $reply = NULL, $replyName = NULL) {
    include_once('Classes/class.phpmailer.php');
    global $mail; //Include pasta/classe do PHPMailer
    if (empty($mail)) {
        $mail = new PHPMailer(); //INICIA A CLASSE
    }
    $mail->ClearAllRecipients();
    $mail->IsSMTP(); //Habilita envio SMPT
    $mail->SMTPAuth = true; //Ativa email autenticado
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    //$mail->SMTPDebug  = 2;
    $mail->SMTPSecure = "tls";
    $mail->Host = MAILHOST; //Servidor de envio
    $mail->Port = MAILPORT; //Porta de envio
    $mail->Username = $from; //email para smtp autenticado
    $mail->Password = base64_decode($pass); //seleciona a porta de envio
    $mail->From = ($from); //remtente
    $mail->FromName = ($fromName); //remtetene nome
    if ($reply) {
        $mail->AddReplyTo(($reply), ($replyName));
    }
    $mail->Subject = ($subject); //assunto
    $mail->Body = ($message); //mensagem
    $mail->AddAddress(($to), ($toName)); //email e nome do destino
    if ($mail->Send()) {
        return true;
    }
}

/* * *********************************
  FUNÇÃO DELETAR TURMAS EXPIRADAS
 * ********************************* */

function expiredClasses() {
    if ($readClasses = read('act_config_class', 'WHERE status=0 AND expiration<=NOW()')) {
        foreach ($readClasses as $class) {
            if ($readLists = read('act_lists', 'WHERE class_id=?', [$class['id']])) {
                foreach ($readLists as $list) {
                    if ($readOrdered = read('act_ordered', 'WHERE list_id=?', [$list['id']])) {
                        foreach ($readOrdered as $ordered) {
                            $readSents = read('act_sent', 'WHERE order_id=?', [$ordered['id']]);
                            foreach ($readSents as $sent) {
                                unlink('./enviados/' . $class['period'] . '/icc-' . $class['class'] . '/' . $sent['file']);
                            }
                            delete('act_sent', 'WHERE order_id=?', [$ordered['id']]);
                            delete('act_ordered', 'WHERE id=?', [$ordered['id']]);
                        }
                    }
                    if (!empty($list['file'])) {
                        $listDirectory = './listas/' . $list['file'];
                        if (file_exists($listDirectory)) {
                            unlink($listDirectory);
                        } elseif (file_exists('.' . $listDirectory)) {
                            unlink('.' . $listDirectory);
                        }
                    }
                    delete('act_lists', 'WHERE class_id=? AND id=?', [$class['id'], $list['id']]);
                }
            }
            delete('act_users', 'WHERE class_id=?', [$class['id']]);
            delete('act_config_class', 'WHERE id=?', [$class['id']]);
        }
    }
}

/* * *********************************
  FUNÇÃO TRATAR NOMES PRÓPRIOS
 * ********************************* */

function uppercaseFirst($fullName) {
    $fullName = explode(" ", $fullName);
    $exceptions = array('da', 'de', 'do', 'das', 'dos', 'e', 'a');
    $output = '';
    foreach ($fullName as $word) {
        if (in_array($word, $exceptions)) {
            $output .= mb_strtolower($word, 'utf-8') . ' ';
        } else {
            $output .= mb_convert_case($word, MB_CASE_TITLE, 'utf-8') . ' ';
        }
    }
    return $output;
}

/* * *********************************
  FUNÇÃO ENCRIPTAR SENHA
 * ********************************* */

function encPswd($string) {
    $enc_string = base64_encode($string);
    $enc_string = str_replace("=", "", $enc_string);
    $enc_string = strrev($enc_string);
    $md5 = md5($string);
    $enc_string = substr($md5, 0, 5) . $enc_string . substr($md5, -5);
    return $enc_string;
}

function msg($msg, $type) {
    echo "<p class=\"msg {$type}\">{$msg}</p>";
}

/* * *********************************
  FUNÇÃO STR_PAD
 * ********************************* */

function addZero($int) {
    return str_pad($int, 2, '0', STR_PAD_LEFT);
}
