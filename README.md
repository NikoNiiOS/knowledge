# Projet de site e-learning "Knowledge Learning"
*Plaftorme E-learning*

## 1 - Prérequis:
- Serveur local: **Xampp**
- IDE: **VsCode**
- Dépendance: **Composer**
- Php: **8.1.25** minimum
- Symfony: **6.4.36**

## 2 - Installation:
Créer un répertoire "knowledge" dans le répertoire "htdocs" dans xampp. Dans VsCode ouvrer votre répertoire et taper dans le terminal ```'git clone https://github.com/NikoNiiOS/knowledge.git '```afin de télécharger le projet.

Taper ensuite dans le terminal ```'composer install'```.

Créez un fichier .env.local à la racine du projet et configurez le:
- ```DATABASE_URL="mysql://root:@127.0.0.1:3306/knowledge?serverVersion=8.0.32&charset=utf8mb4"```
- ```STRIPE_PUBLIC_KEY=pk_test_votre_cle```
- ```STRIPE_SECRET_KEY=sk_test_votre_cle```

Installation de la base de donnée taper:
- ```php bin/console doctrine:database:create```
- ```php bin/console doctrine:migrations:migrate```
- Importez la BDD directement dans phpMyAdmin. (vous trouverez la bdd dans le dossier "Livrables")

Lancez le serveur local de Symfony:
- ```symfony server:start```

## 3 - Connexion et Faire un achat:
- identifiant admin: admin@knowledge.com
- mdp: admin123
  
- code de carte : 4242 4242 4242 4242
- la date / le code secret et le titulaire n'a pas d'importance

## 4 - En cas de problèmes / Troubleshooting
Problème: Message d'erreur d'extension introuvable lors de l'accès au back-office EasyAdmin (Cursus ou Lesson).

Solution: EasyAdmin nécessite l'extension ```intl```.
- Ouvrez le fichier de configuration php.ini de votre XAMPP
- Cherchez la ligne ```;extension=intl``` (crtl + f)
- Enlevez le point-virgule (;) au début pour l'activer
- Redémarrez le serveur Apache sur XAMPP

## 5 - Faire les tests unitaire et fonctionnels

Pour effectué les test il faut d'abord configurez la BDD de test:
- ```php bin/console doctrine:database:create --env=test```
- ```php bin/console doctrine:schema:update --force --env=test```
- ```php bin/console doctrine:fixtures:load --env=test```

Creez un fichier .env.test.local à la racine du projet.

Vous pouvez ensuite lancez les test:
- ```php bin/phpunit```
