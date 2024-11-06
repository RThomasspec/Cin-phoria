



START TRANSACTION;

-- 1. Vérifier si des places sont disponibles
SELECT nb_places FROM salle WHERE id = 1 FOR UPDATE;

-- 2. Si des places sont disponibles, insérer la réservation
INSERT INTO reservation (utilisateur_id, seance_id, commande_id, nb_sieges, prix, statut)
VALUES (1, 5, 3, 2, 20.00, 'confirmée');

-- 3. Mettre à jour le nombre de places disponibles dans la salle
UPDATE salle SET nb_places = nb_places - 2 WHERE id = 1;

-- Fin de la transaction
COMMIT TRANSACTION;


