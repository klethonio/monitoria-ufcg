<?php

/* @author Klethônio Ferreira */
/* * *********************************
  FUNÇÃO GET URL
 * ********************************* */

function getUrl() {
    $urlGet = explode('/', $_GET['url']);
    array_shift($urlGet);
    array_shift($urlGet);
    if (empty($urlGet[0])) {
        $urlGet[0] = 'home';
    }
    if (file_exists('tpl/' . $urlGet[0] . '.php')) {
        require_once('tpl/' . $urlGet[0] . '.php');
    } elseif (!empty($urlGet[1])) {
        if (file_exists('tpl/' . $urlGet[0] . '/' . $urlGet[1] . '.php')) {
            require_once('tpl/' . $urlGet[0] . '/' . $urlGet[1] . '.php');
        } else {
            msg('Página não Encontrada! Navegue no menu.', 'error');
        }
    } else {
        msg('Página não Encontrada! Navegue no menu.', 'error');
    }
}

/* * *********************************
  FUNÇÃO GET USER
 * ********************************* */

function checkUser($user, $minNivel = null, $exact = null) {
    if ($readUser = read('act_users', 'WHERE id=?', [$user])) {
        $user = $readUser[0];
        if ($minNivel === null) {
            return $user['level'];
        } elseif ($user['level'] >= $minNivel && !$exact) {
            return true;
        } elseif ($user['level'] == $minNivel) {
            return true;
        }
    }
}

/* * *********************************
  FUNÇÃO GET DISCIPLINES
 * ********************************* */

/**
 * Retorna disciplinas cadastradas no banco para função preg_match
 * @return string = 'icc|cn|ip'
 */
function returnDisciplines() {
    $readDisciplines = read('act_disciplines');
    $urls = [];
    foreach ($readDisciplines as $discipline) {
        $urls[] = $discipline['url'];
    }
    $disciplines = implode('|', $urls);
    return $disciplines;
}

/* * *********************************
  FUNÇÃO GET USER
 * ********************************* */

function getUser($id, $column = NULL) {
    $readUser = read('act_users', "WHERE id=?", [$id]);
    if ($readUser) {
        $user = $readUser[0];
        if ($column) {
            return $user[$column];
        } else {
            return $user;
        }
    } else {
        return null;
    }
}

/* * *********************************
  FUNÇÃO GET LIST
 * ********************************* */

function getList($id, $column = null) {
    $readList = read('act_lists', "WHERE id=?", [$id]);
    if ($readList) {
        foreach ($readList as $list)
            ;
        if ($column) {
            return $list[$column];
        } else {
            return $list;
        }
    } else {
        return 'Erro ao ler lista';
    }
}

/* * *********************************
  FUNÇÃO GET EXERCÍCIO
 * ********************************* */

function getExer($id, $column = null) {
    $readExer = read('act_ordered', "WHERE id=?", [$id]);
    if ($readExer) {
        foreach ($readExer as $exer)
            ;
        if ($column) {
            return $exer[$column];
        } else {
            return $exer;
        }
    } else {
        return 'Erro ao ler exercício';
    }
}

/* * *********************************
  FUNÇÃO GET ATIVIDADES ENVIADAS
 * ********************************* */

function getSent($user, $ordered, $column = null) {
    $readSent = read('act_sent', "WHERE user_id=? AND order_id=?", [$user, $ordered]);
    if ($readSent) {
        foreach ($readSent as $sent)
            ;
        if ($column) {
            return $sent[$column];
        } else {
            return $sent;
        }
    } else {
        return null;
    }
}

/* * *********************************
  FUNÇÃO GET LIST
 * ********************************* */

function getDiscipline($id, $column = null) {
    $readDiscipline = read('act_disciplines', "WHERE id=?", [$id]);
    if ($readDiscipline) {
        $discipline = $readDiscipline[0];
        if ($column) {
            return $discipline[$column];
        } else {
            return $discipline;
        }
    } else {
        return 'Erro ao ler disciplina';
    }
}

/* * *********************************
  FUNÇÃO GET LANGUAGE
 * ********************************* */

function getLanguage($id, $column = null) {
    $readLanguage = read('act_languages', "WHERE id=?", [$id]);
    if ($readLanguage) {
        $language = $readLanguage[0];
        if ($column) {
            return $language[$column];
        } else {
            return $language;
        }
    } else {
        return 'Erro ao ler linguagem';
    }
}
/* * *********************************
  FUNÇÃO GET CLASS ID
 * ********************************* */

//function getClassId($period, $class) {
//    global $pdo;
//    $readClass = read('act_config_class', "WHERE period=? AND class=?", [$period, $class]);
//    if ($readSent) {
//        $class = $readClass[0];
//
//        return $class['id'];
//    } else {
//        return null;
//    }
//}
