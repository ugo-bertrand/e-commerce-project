# API Symfony e-commerce
### Démarrage de l'API
Tout d'abord il faut vous assurer que sur votre poste vous avez :  

    - Une version de PHP supérieur ou égale à 8.2
    - La dernière version de composer
    - symfony-cli d'installer
Pour lancer l'API sans problème, il faut aussi que votre machine possède une version de mysql idéalement la version 8.  
Dès que la base de données est créer il faut ensuite créer un fichier .env avec les données suivantes :

    - APP_ENV=dev
    - APP_SECRET=2b185b147da72486a69f53a71aaf0171
    - database_hosts=localhost
    - database_port=3306 (ce port est le port par défaut de mysql mais ce paramètre est à verifier en fonction de votre configuration)
    - database_name="e_commerce" (mettre le nom de votre base de données)
    - database_user="user" (mettre le nom de votre utilisateur de la base de données)
    - password="password" (mettre le mot de passe qui correspond avec votre utilisateur)

Lorsque ceci est terminé, placez vous sur la racine du projet en tapant dans votre console la commande suivante ==> cd /e-commerce-project

Ensuite pour installer toutes les dépendances du projet il faut faire la commande ==> composer install (attention composer est nécessaire pour installer les dépendances)

On peut aussi créer la base de données, en utilisant la commande ==> php bin/console doctrine:database:create  
Pour ensuite créer les tables de la base de données on peut utiliser la commande ==> php bin/console make:migration
et cette commande ==> php bin/console doctrine:migrations:migrate

Puis pour lancer l'API, il faut faire la commande ==> symfony server:start