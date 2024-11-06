
# Mini Projet Deefy - Developpement Web - S3

**Développeurs** : Allart Noah et Mangin Adrien

---

### Informations

- Pour exécuter le projet en local, créer un fichier `conf.db.ini` contenant les options de connexion à la base de données, avec la structure suivante :

```ini
driver='mysql'
host='localhost'
database='<nomBD>'
username='root'
password=''
```

- **Pour la BDD** vous avez 2 options :
   - Soit aller sur arche pour récupérer le fichier de création des tables de base.
   - Soit récupérer le fichier présente dans le repository nommé `script_creation_tables.sql`

- Nommer la BDD comme dans le fichier de configuration `<nomBD>` lors de la création de la BDD sur phpMyAdmin.

---

### Fonctionnalités

Les fonctionnalités suivantes sont disponibles dans le menu d'accueil de l'application :

- **Mes playlists** : Affiche la liste des playlists de l'utilisateur authentifié. Chaque playlist est cliquable et permet de la sélectionner, la rendant ainsi "courante" (stockée en session).

- **Créer une playlist vide** : Permet de créer une nouvelle playlist via un formulaire où l'utilisateur saisit le nom de la playlist. Une fois validée, la playlist est enregistrée en BDD et devient la playlist courante.

- **Afficher la playlist courante** : Affiche la playlist stockée en session.

- **S’inscrire** : Permet de créer un compte utilisateur avec le rôle **STANDARD**.

- **S’authentifier** : Permet à l'utilisateur de se connecter en fournissant ses identifiants.

- **Affichage et gestion des playlists** :
   - L'affichage et l'ajout de pistes à une playlist sont réservés au propriétaire de la playlist ou aux utilisateurs ayant le rôle **ADMIN**.
   - **Compte admin préexistant** :
      - **Email** : `admin@mail.com`
      - **Mot de passe** : `admin`

---

### Sécurité

- Stockage sécurisé des mots de passe.
- Protection contre les injections XSS et SQL.
- Code HTML valide

---
