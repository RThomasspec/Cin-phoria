
-- Commencer une transaction
START TRANSACTION;

-- Insérer un cinema
INSERT INTO cinema (nom, adresse, gsm) 
VALUES ('Cinéphoria Nantes', '12 Pl. du Commerce, Nantes', '04 13 13 13 13');

-- Récupérer l'ID du cinema inséré
SET @cinema_id = LAST_INSERT_ID();

-- Insérer une salle
INSERT INTO salle (nom, qualite, nb_places, cinema_id) 
VALUES ('Salle 7', 'STANDARD', 20, @cinema_id);

-- Récupérer l'ID de la salle insérée
SET @salle_id = LAST_INSERT_ID();

-- Insérer des horaires pour la salle
INSERT INTO horaire (salle_id, jour, debut, fin) 
VALUES
    (@salle_id, 'Lundi', '10:00:00', '12:00:00'),
    (@salle_id, 'Mardi', '14:00:00', '16:00:00'),
    (@salle_id, 'Mercredi', '09:00:00', '16:00:00'),
    (@salle_id, 'Jeudi', '11:00:00', '15:00:00');

-- Si toutes les opérations se sont bien déroulées, valider la transaction
COMMIT;

-- En cas d'erreur, annuler la transaction
ROLLBACK;
