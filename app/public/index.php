<?php

require_once __DIR__.'/../vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

try {
    $db = new FirestoreClient([
        'projectId' => 'b3-firebase-d7513',
    ]);

    // Récupérer les documents triés par date en ordre décroissant
    $collectionReference = $db->collection('factures');
    $query = $collectionReference->orderBy('date', 'DESCENDING'); // Tri par date décroissante
    $documents = $query->documents();
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
</head>
<body>

<div class="container">
    <h1>Liste des Factures</h1>
    <table border=1 style="width: 100%;">
        <thead>
            <tr>
                <th>Id</th>
                <th>Numéro</th>
                <th>Date</th>
                <th>Prix</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $date = isset($data["date"]) && $data["date"] instanceof Google\Cloud\Core\Timestamp
                        ? $data["date"]->get()->format('d/m/Y H:i:s') // Conversion de Timestamp en format français
                        : 'Date invalide';

                    $number = htmlspecialchars($data['number'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8');
                    $cost = htmlspecialchars($data['cost'] ?? '0', ENT_QUOTES, 'UTF-8');
                    $status = htmlspecialchars($data['status'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc->id(), ENT_QUOTES, 'UTF-8'); ?></td>
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
</body>
</html>
