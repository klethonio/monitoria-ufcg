<?php
/* @author Klethônio Ferreira */
global $actUser, $class_url, $class;
$name = explode(' ', $actUser['name']);
$name = $name[0];
?>
<div id="data-user">
    <h1>Bem Vindo, <?= $name ?>!</h1>
    <?php
    if (!empty($_SESSION['login'])) {
        msg('Você está logado!', 'infor');
        unset($_SESSION['login']);
    }
    $statusUser = checkUser($actUser['id']);
    $level = $actUser['level'] == 2 ? 'Professor' : ($actUser['level'] == 1 ? 'Monitor' : 'Aluno');

    //GERADAS EM header.php
    global $orderPeding, $readSent, $readSols;

    if ($orderPeding) {
        msg('Você tem ' . $orderPeding . ' atividade(s) pendente(s).', 'alert');
    } elseif ($statusUser == 0) {
        msg('Você não tem atividades pendentes', 'infor');
    }

    if ($readSent) {
        msg('Você tem ' . count($readSent) . ' atividade(s) pendente(s) para corrigir!', 'alert');
    } elseif ($statusUser == 1) {
        msg('Você não tem atividades pendentes para corrigir', 'infor');
    }

    if ($readSols) {
        msg('Você tem ' . count($readSols) . ' solicitações de usuários!', 'alert');
    }
    ?>
    <p><b class="right">Usuário:</b> <?= $actUser['register'] ?> - <?= $actUser['name'] ?></p>
    <?php
    $readLists = read('act_lists', 'WHERE class_id=?', [$class['id']]);
    $readOrdereds = read('act_ordered', 'JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=?', [$class['id']]);
    if ($statusUser == 0) {
        echo '<p style="background: #eee;"><b class="right">Curso:</b> ' . $actUser['course'] . '</p>';
        echo '<p><b class="right">Nível:</b> ' . $level . '</p>';
        $readSents = read('act_sent', 'WHERE user_id=?', [$actUser['id']]);
        echo '<hr />';
        echo '<p style="background: #eee;"><b class="right">Número de Listas:</b> ' . count($readLists) . '</p>';
        echo '<p><b class="right">Exercícios Requisitados:</b> ' . count($readOrdereds) . '</p>';
        echo '<p style="background: #eee;"><b class="right">Exercícios Enviados:</b> ' . count($readSents) . '</p>';
    } else {
        if ($statusUser == 1) {
            echo '<p style="background: #eee;"><b class="right">Curso:</b> ' . $actUser['course'] . '</p>';
        }
        echo '<p><b class="right">Nível:</b> ' . $level . '</p>';
        $readUsers = read('act_users', 'WHERE class_id=? AND level=0', [$class['id']]);
        $readSents = read('act_sent', 'JOIN act_users AS u ON u.id = a.user_id WHERE u.class_id=?', [$class['id']]);
        echo '<h3>Relatório Rápido</h3>';
        echo '<hr />';
        echo '<p style="background: #eee;"><b class="right">Número de Listas:</b> ' . count($readLists) . '</p>';
        echo '<p><b class="right">Quant. de Alunos:</b> ' . count($readUsers) . '</p>';
        echo '<p style="background: #eee;"><b class="right">Exercícios Requisitados:</b> ' . count($readOrdereds) . '</p>';
        echo '<p><b class="right">Exercícios Esperados:</b> ' . (count($readUsers) * count($readOrdereds)) . '</p>';
        echo '<p style="background: #eee;"><b class="right">Exercícios Recebidos:</b> ' . count($readSents) . '</p>';
        $totalCorrecteds = 0;
        if ($readSents = read('act_sent', 'JOIN act_users AS u ON u.id = a.user_id WHERE u.class_id=? AND a.corrected_by IS NOT NULL GROUP BY a.corrected_by', [$class['id']])) {
            foreach ($readSents as $sent) {
                if ($monitor = getUser($sent['corrected_by'])) {
                    $monitorName = explode(' ', $monitor['name']);
                    $monitorName = $monitorName[0];
                } else {
                    $monitorName = 'Desconhecido';
                }
                $readCorrecteds = read('act_sent', 'WHERE corrected_by=?', [$sent['corrected_by']]);
                echo '<p><b class="right">Corrigidos por ' . $monitorName . ':</b> ' . count($readCorrecteds) . '</p>';
                $totalCorrecteds += count($readCorrecteds);
            }
        }
        echo '<p style="background: #eee; margin-bottom: 15px;"><b class="right">Total Corrigidos:</b> ' . $totalCorrecteds . '</p>';
    }
    ?>
</div>
<?php
if ($statusUser != 0) {
    $statsOrdereds = read('act_ordered', 'JOIN act_lists AS l ON l.id = a.list_id WHERE l.class_id=? ORDER BY a.end_date DESC LIMIT 10', [$class['id']]);
    if ($statsOrdereds) {
        $statsOrdereds = array_reverse($statsOrdereds);
        $medias = [];
        $missed = [];
        foreach ($statsOrdereds as $ordered) {
            $soma = 0;
            $legend = 'L' . getList($ordered['list_id'], 'num') . 'Ex' . $ordered['num'];
            $readSent = read('act_sent', 'WHERE order_id=? AND grade IS NOT NULL', [$ordered['id']]);
            if ($readSent) {
                foreach ($readSent as $sent) {
                    $soma += $sent['grade'];
                }
                $media = $soma / count($readSent);
                $medias[$legend] = round($media, 1);
            }
            $readSentFull = read('act_sent', 'WHERE order_id=?', [$ordered['id']]);
            $missed[$legend] = count($readUsers) - count($readSentFull);
        }
        $contOne = 0;
        $contTwo = 0;
        $contTree = 0;
        foreach ($readOrdereds as $ordered) {
            $readSents = read('act_sent', 'WHERE order_id=? AND grade IS NOT NULL', [$ordered['id']]);
            if ($readSents) {
                foreach ($readSents as $sent) {
                    if ($sent['grade'] < 5) {
                        $contOne += 1;
                    } elseif ($sent['grade'] < 7) {
                        $contTwo += 1;
                    } else {
                        $contTree += 1;
                    }
                }
            }
        }
        ?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script>
            google.charts.load('current', {packages: ['corechart', 'bar']});
            google.charts.setOnLoadCallback(drawBasic);
            function drawBasic() {
        <?php
        if ($medias) {
            ?>

                    var data1 = new google.visualization.arrayToDataTable([
                        ['Exercícios', 'Nota', {role: 'style'}],
            <?php
            foreach ($medias as $legend => $grade) {
                if ($grade < 5) {
                    $color = 'red';
                } elseif ($grade < 7) {
                    $color = 'yellow';
                } else {
                    $color = 'green';
                }
                echo "['" . $legend . "', " . $grade . ", '" . $color . "'],";
            }
            ?>
                    ]);
                    var options1 = {
                        dataOpacity: 0.6,
                        title: 'Média das Últimas Atividades',
                        legend: {position: 'none'},
                        bar: {groupWidth: '40%'},
                        vAxis: {
                            viewWindow: {
                                min: 0,
                                max: 10
                            }
                        }
                    };
                    var chart1 = new google.visualization.ColumnChart(document.getElementById('last_ordered'));
                    chart1.draw(data1, options1);
            <?php
        }
        if ($missed) {
            ?>
                    var data2 = new google.visualization.arrayToDataTable([
                        ['Atividade', 'Faltou: '],
            <?php
            foreach ($missed as $legend => $total) {
                echo "['" . $legend . "', " . $total . "],";
            }
            ?>
                    ]);
                    var options2 = {
                        title: 'Número de atividades não enviadas',
                        legend: {position: 'none'},
                        bar: {groupWidth: '40%'}
                    };
                    var chart2 = new google.visualization.LineChart(document.getElementById('missed_ordered'));
                    chart2.draw(data2, options2);
            <?php
        }

        if ($contOne || $contTwo || $contTree) {
            $chart3 = TRUE;
            ?>

                    var data3 = google.visualization.arrayToDataTable([
                        ['Faixa', 'Porcentagem'],
                        ['Menor que 5', <?= $contOne ?>],
                        ['Entre 5 e 7', <?= $contTwo ?>],
                        ['Maior/igual 7', <?= $contTree ?>]
                    ]);

                    var options3 = {
                        title: 'Média Geral da Turma',
                        sliceVisibilityThreshold: 0,
                        is3D: true,
                        slices: {
                            0: {color: '#ff3333'},
                            1: {color: '#ffff33'},
                            2: {color: '#009933'}
                        }
                    };

                    var chart3 = new google.visualization.PieChart(document.getElementById('total_ordered'));
                    chart3.draw(data3, options3);
            <?php
        }
        ?>
            }
        </script>
        <?php
    }
    ?>
        <h2>Estatísticas da Disciplina: </h2>
        <?php
        if (!empty($medias)) {
            echo '<div class="statistics"><div style="width:550px; height:200px;" id="last_ordered"></div></div>';
        } 
        if (!empty($missed)) {
            echo '<div class="statistics"><div style="width:550px; height:200px;" id="missed_ordered"></div></div>';
        }
        if (!empty($chart3)) {
            echo '<div class="statistics"><div style="width:550px; height:200px;" id="total_ordered"></div></div>';
        }
        if (empty($medias) && empty($missed) && empty($chart3)) {
            msg('Aguardando novos dados!', 'infor');
        }
        ?>
    <?php
}
?>