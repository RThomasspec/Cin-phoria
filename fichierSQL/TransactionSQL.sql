


START TRANSACTION;

-- Insertion des cinémas
INSERT INTO `cinema` (`id`, `nom`, `adresse`, `gsm`) VALUES
(1, 'Cinéphoria de Nantes', '12 Pl. du Commerce, Nantes', '04 94 11 11 11'),
(2, 'Cinéphoria de Bordeaux', '15 Rue Georges Bonnac, Bordeaux', '04 94 12 12 12'),
(3, 'Cinéphoria de Paris', '83 Bd du Montparnasse, Paris', '04 94 13 13 13'),
(4, 'Cinéphoria de Toulouse', '3 Pl. du Président Thomas Wilson, Toulouse', '04 94 14 14 14'),
(5, 'Cinéphoria de Lille', '26 Rue des Ponts de Comines, Lille', '04 94 15 15 15'),
(6, 'Cinéphoria de Charleroi', 'Quai Arthur Rimbaud 10, 6000 Charleroi', '04 94 16 16 16'),
(7, 'Cinéphoria de Liège', 'Pl. Xavier-Neujean 14, 4000 Liège', '04 94 17 17 17');


-- Insertion des films
INSERT INTO `film` (`id`, `titre`, `description`, `age_minimum`, `coup_de_coeur`, `note`, `affichage`, `id_image`, `genre`) VALUES
(1, 'Deadpool & Wolverine', 'Wolverine se remet de ses blessures lorsqu\'il croise le chemin de la grande gueule, Deadpool, qui a voyagé dans le temps pour le soigner dans l\'espoir de devenir amis et de faire équipe pour vaincre un ennemi commun.', 12, 1, NULL, '/Applications/MAMP/tmp/php/phppTtSR6', '66b237011cf93.jpg', 'Fantastique'),
(2, 'Joker : Folie à deux', 'Le comédien raté Arthur Fleck rencontre l\'amour de sa vie, Harley Quinn, alors qu\'il est incarcéré à l\'hôpital d\'État d\'Arkham. À sa sortie, tous deux se lancent dans une aventure romantique vouée à l\'échec.', 12, 1, NULL, '/Applications/MAMP/tmp/php/php7jQnuX', '66fc389462409.jpg', 'Drame'),
(3, 'Beetlejuice Beetlejuice', 'À la suite d\'une tragédie familiale inattendue, trois générations de Deetz reviennent à la maison de Winter River.', 7, 0, NULL, '/Applications/MAMP/tmp/php/phpqchDYc', '66fc390b0cc84.jpg', 'Horreur'),
(4, 'Megalopolis', 'Megalopolis est une épopée romaine dans une Amérique moderne imaginaire en pleine décadence. La ville de New Rome doit absolument changer, ce qui crée un conflit majeur entre César Catilina, artiste de génie ayant le pouvoir d\'arrêter le temps, et le maire archi-conservateur Franklyn Cicero. La fille du maire et jet-setteuse Julia Cicero, amoureuse de César Catilina, est tiraillée entre les deux hommes et devra découvrir ce qui lui semble le meilleur pour l\'avenir de l\'humanité.', 12, 0, NULL, '/Applications/MAMP/tmp/php/phpbAT8p6', '66fc397050385.jpg', 'Fantastique'),
(5, 'Ni chaînes ni maîtres', '1759, sur l\'Isle de France, actuelle Île Maurice. Massamba et Mati, esclaves dans la plantation d\'Eugène Larcenet, vivent dans la peur et le labeur. Lui rêve que sa fille soit affranchie, elle de quitter l\'enfer vert de la canne à sucre. Une nuit, elle s\'enfuit. Madame La Victoire, célèbre chasseuse d\'esclaves, est engagée pour la traquer. Massamba n\'a d\'autre choix que de s\'évader à son tour. Par cet acte, il devient un \"marron\", un fugitif qui rompt à jamais avec l\'ordre colonial.', 12, 0, NULL, '/Applications/MAMP/tmp/php/php5XNbCo', '66fc3a9019d9f.jpg', 'Drame'),
(6, 'On fait quoi maintenant ?', 'Brutalement licencié à 58 ans, Alain décide de monter sa propre société pour se prouver et prouver au monde qu\'il n\'est pas devenu inutile. Embarquant dans son improbable projet Véronique, son ancienne collègue bloquée depuis des années dans la dépression, et Jean-Pierre Savarin, un animateur de jeu télévisé sur le retour, il s\'attaque au secteur qu\'il pense être le plus porteur : la garde d\'enfants.', 12, 0, NULL, '/Applications/MAMP/tmp/php/phpu1YuHk', '66fc3b90f32df.jpg', 'Comédie'),
(7, 'TRAP', 'Un père et sa fille adolescente assistent à un concert pop, où ils réalisent qu\'ils sont au centre d\'un événement sombre et sinistre.', 12, 0, NULL, '/Applications/MAMP/tmp/php/phpNRSP8f', '66fc3bf28fa9a.png', 'Horreur');


-- Insertion des diffusions
INSERT INTO `diffusion` (`id`, `cinemas_id`, `films_id`) VALUES
(127, 1, 1),
(128, 2, 2),
(129, 3, 3),
(130, 4, 4),
(131, 5, 5),
(132, 6, 6),
(133, 7, 7),



-- Fin de la transaction
COMMIT TRANSACTION;
