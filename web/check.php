<?php
const TABLES = [
    'contrat_etudiant',
    'contrat_labo',
    'contrat_vacataire',
    'donation',
    'entreprise',
    'entreprise_site',
    'etudiant',
    'laboratoire',
    'employe',
    'site',
    'utilisateur',
];

function checkTable(PDO $pdo, string $table): bool | Exception {
    try {
        $req = $pdo->prepare("SELECT * FROM ".$table." LIMIT 1");
        $req->execute();
        return true;
    } catch (Exception $e) {
        return $e;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Check your Docker stack</title>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            align-items: center;
            justify-content: center;
            font-family: "Open Sans", sans-serif;
        }
        .success {
            background-color: #24b621;
            color: whitesmoke;
            padding: 20px;
            border-radius: 10px;
        }
        .failed {
            background-color: #b63732;
            color: whitesmoke;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php
    try {
        $pdo = new PDO("pgsql:host=db;dbname=netyparneo", "postgres", "OwOUWUFurry");
        ?>
        <p class="success">Connection à la base de données réussi !</p>
        <table border="1">
            <thead>
                <th>Table</th>
                <th>Status</th>
            </thead>
            <tbody>
        <?php
        $startTime = doubleval(microtime());
        foreach (TABLES as $table) {
            $res = checkTable($pdo, $table);
            echo("<tr><td>".$table."</td>");
            if ($res === true) {
                ?>
                <td style="background-color: #24b621; color: whitesmoke;">présente</td>
    <?php
            } else {
                ?>
                <td style="background-color: #b63732; color: whitesmoke;">inexistante</td>
    <?php
            }
            echo("</tr>");
        }
        ?>
            </tbody>
        </table>
    <?php
        echo("Execution time: ".((doubleval(microtime()) - $startTime) * 1000)."ms");
    } catch (Exception $e) {
        ?>
        <p class="failed">La connection à la base de données a échoué !<br>Erreur: <?=$e->getMessage()?></p>
    <?php
    }
    ?>
</body>
</html>