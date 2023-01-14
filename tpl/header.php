<?php
/* @author Klethônio Ferreira */
global $actUser, $urlGet, $class;
if (!function_exists('getUser')) {
    header('Location: ' . BASE);
}
$statusUser = checkUser($actUser['id']);
///////////////////////////////
//NOTIFICAÇÕES
///////////////////////////////
$orderPeding = 0;
$readSent = [];
if ($statusUser == 0) {
    if ($readOrdereds = read('act_ordered', 'JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? AND a.end_date>=NOW()+1 ORDER BY a.end_date', [$class['id']])) {
        foreach ($readOrdereds as $ordered) {
            if (!read('act_sent', 'WHERE user_id=? AND order_id=?', [$actUser['id'], $ordered['id']])) {
                $orderPeding += 1;
            }
        }
    }
} elseif ($statusUser == 1) {
    $readSent = read('act_sent', 'JOIN act_users AS u ON u.id = a.user_id WHERE u.class_id=? AND a.grade is NULL', [$class['id']]);
} else {
    $readSols = read('act_users', 'WHERE class_id=? AND status=0 AND level<>2', [$class['id']]);
}
///////////////////////////////
?>
<nav id="menu">
    <ul>
        <li><a class="btn-home" href="<?= BASE . '/' . $urlGet ?>/home">Home</a></li>
        <li><a href="#" rel="false">Atividades &blacktriangledown;
                <?php
                if ($orderPeding) {
                    echo '<small>' . $orderPeding . '</small>';
                } elseif ($readSent) {
                    echo '<small>' . count($readSent) . '</small>';
                }
                ?>
            </a>
            <ul>
                <?php
                if ($statusUser == 0) {
                    ?>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/atividades/minhas-atividades">Minhas Atividades</a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/atividades/enviar">Enviar Atividades
                            <?php
                            if ($orderPeding) {
                                echo '<small>' . $orderPeding . '</small>';
                            }
                            ?>
                        </a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/atividades/listas">Listas</a></li>
                    <?php
                }
                if ($statusUser >= 1) {
                    ?>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/listas/gerenciar">Requisitar Atividades</a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/atividades/requisitadas">Atividades Requisitadas</a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/atividades/corrigir-atividades">Corrigir Atividades
                            <?php
                            if ($readSent) {
                                echo '<small>' . count($readSent) . '</small>';
                            }
                            ?>
                        </a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/atividades/gerar-relatorio">Relatórios</a></li>
                    <?php
                }
                ?>
            </ul>
        </li>
        <?php
        if ($statusUser == 2) {
            ?>
            <li><a href="#" rel="false">Listas &blacktriangledown;</a>
                <ul>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/listas/adicionar">Adicionar Lista</a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/listas/gerenciar">Gerenciar Listas</a></li>
                </ul>
            </li>
            <?php
        }
        if ($statusUser == 2) {
            ?>
            <li><a href="#" rel="false">Orientandos &blacktriangledown;
                    <?php
                    if ($readSols) {
                        echo '<small>' . count($readSols) . '</small>';
                    }
                    ?>
                </a>
                <ul>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/orientandos/adicionar">Adicionar Orientando</a></li>
                    <li><a href="<?= BASE . '/' . $urlGet ?>/orientandos/gerenciar">Gerenciar Orientandos
                            <?php
                            if ($readSols) {
                                echo '<small>' . count($readSols) . '</small>';
                            }
                            ?>
                        </a></li>
                </ul>
            </li>
            <?php
        }
        ?>
        <li><a href="<?= BASE . '/' . $urlGet ?>/<?php
            if ($statusUser == 2) {
                echo 'professor/';
            }
            ?>perfil">Perfil</a></li>
        <li><a href="<?= BASE . '/' . $urlGet ?>/logout">Sair</a></li>
    </ul>
</nav>