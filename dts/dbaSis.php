<?php

/* @author Klethônio Ferreira */
require('iniSis.php');

$pdo = new PDO('mysql:host=' . HOST . ';dbname=' . DBSA, USER, PASS);

$pdo->query("SET NAMES 'utf8'; "
        . "SET character_set_connection=utf8; "
        . "SET character_set_client=utf8;"
        . "SET character_set_results=utf8;"
        . "SET time_zone = 'America/Fortaleza'");

$pdo->prepare("set session sql_mode='NO_ENGINE_SUBSTITUTION'")->execute();
/* * *********************************
  FUNÇÃO CADASTRAR
 * ********************************* */

function create(string $table, array $data) {
    global $pdo;
    $columns = implode(", ", array_keys($data));
    $values = ':' . implode(', :', array_keys($data));
    $stCreate = $pdo->prepare("INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")");
    if ($stCreate->execute($data)) {
        return $pdo->lastInsertId();
    }
}

/* * *********************************
  FUNÇÃO LER
 * ********************************* */

function read($table, $cond = NULL, array $params = NULL) {
    global $pdo;
    $stRead = $pdo->prepare("SELECT a.* FROM " . $table . " AS a " . $cond);
    if ($params) {
        $i = 1;
        foreach ($params as $key => &$value) {
            if (is_int($value)) {
                $value = (int) $value;
            }
            $stRead->bindValue($i, $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            $i++;
        }
    }
    $stRead->execute();
    if ($stRead->rowCount() > 0) {
        $result = $stRead->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } else {
        return [];
    }
}

/* * *********************************
  FUNÇÃO ATUALIZAR
 * ********************************* */

function update(string $table, array $data, $cond, array $params) {
    global $pdo;
    $fields = implode('=?, ', array_keys($data)) . '=?';
    $stUpdate = $pdo->prepare("UPDATE " . $table . " SET " . $fields . " " . $cond);
    $stUpdate->execute(array_merge(array_values($data), $params));
    if ($stUpdate->rowCount()) {
        return $stUpdate->rowCount();
    }
}

/* * *********************************
  FUNÇÃO DELETAR
 * ********************************* */

function delete(string $table, $cond, array $params) {
    global $pdo;
    $stDelete = $pdo->prepare("DELETE FROM " . $table . " " . $cond);
    if ($stDelete->execute($params)) {
        return true;
    }
}

function getSelectAndFrom(string|array $table) {
    if (is_string($table)) {
        return ['a.*', $table];
    } else {
        return [array_values($table)[0], array_keys($table)[0]];
    }
}
