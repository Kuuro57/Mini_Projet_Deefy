# Mini_Projet_Deefy
Mini projet Deefy - Dev Web - Allart Noah et Mangin Adrien

Info :
   - Avoir sur votre projet local un fichier conf.db.ini (option de connexion à la BDD)

de la forme :

driver='mysql'
host='<hosy>'
database='<nomBD>'
username='<username>'
password='<mdp>'


Les fonctionnalités suivantes sont accessibles dans le menu d’accueil de l’application :

• mes playlists : affiche la liste des playlists de l’utilisateur authentifié ; chaque élément de la
liste est cliquable et permet d’afficher une playlist qui devient la playlist courante ; stockée
en session,
• créer une playlist vide : un formulaire permettant de saisir le nom d’une nouvelle playlist est
affiché. A la validation, la playlist est créée et stockée en BD ; elle devient la playlist
courante.
• Afficher la playlist courante : affiche la playlist stockée en session,
• S’inscrire : création d’un compte utilisateur avec le rôle STANDARD
• s’authentifier : fournir ses credentials pour s’authentifier en tant qu’utilisateur enregistré.

• L’affichage d’une playlist et l’ajout d’une piste à une playlist est réservé au propriétaire de la
playlist ou au rôle ADMIN (Le compte admin est déjà créer en BDD, email:admin@mail.com mdp:admin)
• Sécurité : stockage adéquat des mot de passe,
parades contre l’injection XSS et SQL,
• le code HTML généré valide,

