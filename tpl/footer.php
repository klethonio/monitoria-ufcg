<footer>
    <p>Departamento de Sistemas e Computação</p>
    <?php
    $datetime_query = $pdo->query('SELECT NOW()');
    $datetime_sql = $datetime_query->fetch(PDO::FETCH_ASSOC);
    $datetime_sql = explode(' ', $datetime_sql['NOW()']);
    $date = explode('-', $datetime_sql[0]);
    $datetime = $date[2] . '/' . $date[1] . '/' . $date[0] . ' ' . $datetime_sql[1];

    if (empty($_GET['url']) || $_GET['url'] == 'creditos') {
        $credit_url = BASE . '/creditos';
    } else {
        $credit_url = BASE . '/' . $urlGet . '/creditos';
    }
    ?>
    <small>Plataforma: <?= date('d/m/Y H:i:s') ?> - Base de Dados: <?= $datetime ?></small>
    <small>Design inspirado em: <a href="https://pre.ufcg.edu.br:8443/ControleAcademicoOnline" target="_blank">Contre Acadêmico UFCG</a> | <a href="<?= $credit_url ?>">Créditos</a></small>
</footer>