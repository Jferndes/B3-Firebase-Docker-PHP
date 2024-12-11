<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new FirestoreClient([
            'projectId' => 'b3-firebase-d7513',
        ]);

        // Récupération des données du formulaire
        $number = $_POST['number'] ?? '';
        $cost = (float)($_POST['cost'] ?? 0);
        $status = $_POST['status'] ?? 'Inconnu';

        // Création d'un timestamp
        $timestamp = new Timestamp(new DateTime());

        // Ajout dans Firestore
        $collectionReference = $db->collection('factures');
        $collectionReference->add([
            'number' => $number,
            'date' => $timestamp, // Ajout automatique de la date avec heure
            'cost' => $cost,
            'status' => $status,
        ]);

        // Redirection vers index.php
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $errorMessage = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Facture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container">
        <h1>Ajouter une Nouvelle Facture</h1>

        <?php if (!empty($errorMessage)): ?>
            <article class="error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></article>
        <?php endif; ?>

        <form method="POST" action="ajouter_facture.php" onsubmit="handleSubmit(event)">
            <label for="number">
                Numéro de Facture :
                <input type="text" id="number" name="number" placeholder="Ex: F2024-001" required>
            </label>

            <label for="cost">
                Prix (€) :
                <input type="number" id="cost" name="cost" step="0.01" placeholder="Ex: 123.45" required>
            </label>

            <label for="status">
                Statut :
                <select id="status" name="status" required>
                    <option value="Payée">Payée</option>
                    <option value="Impayée">Impayée</option>
                    <option value="En Attente">En Attente</option>
                </select>
            </label>

            <button type="submit" id="submitButton" class="contrast">Ajouter Facture</button>
            <a href="index.php" role="button" class="secondary">Retour à la Liste</a>
        </form>
    </main>

    <script>
        function handleSubmit(event) {
            // Désactive le formulaire pour éviter les double soumissions
            const submitButton = document.getElementById('submitButton');
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.setAttribute('aria-label', 'Veuillez patienter...');
            submitButton.textContent = 'En cours...';
        }
    </script>
</body>
</html>