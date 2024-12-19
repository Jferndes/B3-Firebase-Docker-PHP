<?php

require_once __DIR__.'/../vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

$orderByField = 'date';  // Champ de tri par défaut
$orderDirection = 'DESCENDING';  // Direction du tri par défaut

if (isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['number', 'date', 'cost', 'status'])) {
    $orderByField = $_GET['sort_by']; // Récupère le champ de tri
}

if (isset($_GET['order']) && ($_GET['order'] == 'ASC' || $_GET['order'] == 'DESC')) {
    $orderDirection = $_GET['order']; // Récupère la dszirection du tri (ASC ou DESC)
}

try {
    $db = new FirestoreClient([
        'projectId' => 'b3-firebase-d7513',
    ]);a

    // Récupérer les documents triés par le champ sélectionné
    $collectionReference = $db->collection('factures');
    $documents = $collectionReference->orderBy($orderByField, $orderDirection)->documents();
} catch (Exception $e) {
    die('Erreur lors de la connexion à Firestore : ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Factures</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        th {
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Factures</h1>
    <table border=1 style="width: 100%;">
        <thead>
            <tr>
                <th onclick="sortTable('number')">Numéro</th>
                <th onclick="sortTable('date')">Date</th>
                <th onclick="sortTable('cost')">Prix</th>
                <th onclick="sortTable('status')">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $date = isset($data["date"])
                        ? $data["date"]->get()->format('d/m/Y H:i:s') // Conversion de Timestamp en format français
                        : 'Date invalide';

                    $number = $data['number'];
                    $cost = $data['cost'];
                    $status = $data['status'];
                    ?>
                    <tr>
                        <td><?php echo $number; ?></td>
                        <td><?php echo $date; ?></td>
                        <td><?php echo $cost; ?> €</td>
                        <td><?php echo $status; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
    <div style="margin-bottom: 20px;">
        <button onclick="window.location.href='ajouter_facture.php'" style="padding: 10px 20px; font-size: 16px;">Ajouter une Nouvelle Facture</button>
    </div>
</div>

<script>
    function sortTable(field) {
        const urlParams = new URLSearchParams(window.location.search);
        let order = 'ASC';
        if (urlParams.get('sort_by') === field && urlParams.get('order') === 'ASC') {
            order = 'DESC';
        }
        urlParams.set('sort_by', field);
        urlParams.set('order', order);
        window.location.search = urlParams.toString();
    }
</script>

</body>
</html>
