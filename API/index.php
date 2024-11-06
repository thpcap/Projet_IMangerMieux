<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Documentation API - user.php</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: whitesmoke;
                color: #333;
                margin: 0;
                padding: 20px;
            }
            .container {

                max-width: 1500px;
                margin: auto;
                background: beige;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            }
            h1, h2, h3 {
                color: #3498db;
            }
            .endpoint {
                margin-bottom: 20px;
                background-color: bisque;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                padding: 15px;
                border-radius: 20px;
            }
            .method {
                color: #e74c3c;
                font-weight: bold;
            }
            .code {
                font-family: monospace;
                background: #f9f9f9;
                padding: 5px 10px;
                border-radius: 5px;
            }
            .JSON {
                background-color: beige;
                padding: 10px;
                font-family: monospace;
                border-left: 4px solid #3498db;
                margin-top: 10px;
            }
            ul {
                list-style: none;
                padding: 0;
            }
            ul li {
                margin-bottom: 5px;
            }
            .param {
                font-weight: bold;
            }
            #title {
                display: flex;
                flex-direction: column; /* Pour empiler l'image et le titre verticalement */
                align-items: center; /* Centre horizontalement */
            }

            #title img {
                max-width: 100%; /* L'image s'ajuste à la largeur du conteneur sans déformer */
                height: auto; /* Conserve les proportions de l'image */
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div id="title">
                <img src="upper_logo.png" alt="">
                <h1>Documentation API IMangerMieux</h1>
            </div>
            
            <div class="endpoint">
                <h2>POST - Connexion de l'utilisateur</h2>
                <p><span class="method">POST</span> <code>/API/login.php</code></p>
                <p>Permet à un utilisateur de se connecter à l'application.</p>
                <h3>Paramètres JSON</h3>
                <ul>
                    <li><span class="param">login</span> (requis) : Identifiant de l'utilisateur.</li>
                    <li><span class="param">motDePasse</span> (requis) : Mot de passe de l'utilisateur.</li>
                </ul>

                <h3>Exemple de corps de requête</h3>
                <pre class="JSON">
                    {
                        "login": "john_doe",
                        "motDePasse": "password123"
                    }
                </pre>
                <h3>Cookies utilisés</h3>
                <p>aucun</p>
                
                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">connected</span>: Etat de la connection</li>
                </ul>
                              
                <pre class="JSON">
                    {
                        "connected": true
                    }
                </pre>

                <h3>Cookies ajoutés</h3>
                <ul>
                    <li><strong>PHPSESSID</strong>:Cookie de session</li>
                    <li><strong>login</strong>:login de l'utilisateur</li>
                </ul>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Connexion réussie.</li>
                    <li>400 Bad Request : Le login ou le mot de passe est manquant.</li>
                    <li>401 Unauthorized : Identifiants incorrects.</li>
                    <li>405 Method Not Allowed : Méthode HTTP autre que POST utilisée.</li>
                    <li>500 Internal Server Error : Erreur interne de la base de données.</li>
                </ul>
            </div>
            
            <div class="endpoint">
                <h2>POST - Inscription d'un nouvel utilisateur</h2>
                <p><span class="method">POST</span> <code>/API/create_user.php</code></p>
                <p>Permet l'inscription d'un nouvel utilisateur dans l'application.</p>
                
                <h3>Paramètres JSON</h3>
                <ul>
                    <li><span class="param">login</span> (requis) : Identifiant unique de l'utilisateur.</li>
                    <li><span class="param">motDePasse</span> (requis) : Mot de passe de l'utilisateur.</li>
                    <li><span class="param">email</span> (requis) : Adresse e-mail de l'utilisateur.</li>
                    <li><span class="param">nom</span> (requis) : Nom de famille de l'utilisateur.</li>
                    <li><span class="param">prenom</span> (requis) : Prénom de l'utilisateur.</li>
                    <li><span class="param">sexe</span> (requis) : ID du sexe de l'utilisateur, doit correspondre à un enregistrement valide dans la base de données.</li>
                    <li><span class="param">niveauPratique</span> (requis) : ID du niveau de pratique de l'utilisateur, doit correspondre à un enregistrement valide dans la base de données.</li>
                    <li><span class="param">date</span> (requis) : Date de naissance de l'utilisateur au format "YYYY-MM-DD", doit être une date passée.</li>
                </ul>

                <h3>Exemple de corps de requête</h3>
                <pre class="JSON">
                    {
                        "login": "jane_doe",
                        "motDePasse": "securepassword",
                        "email": "jane.doe@example.com",
                        "nom": "Doe",
                        "prenom": "Jane",
                        "sexe": 1,
                        "niveauPratique": 3,
                        "date": "1990-05-15"
                    }
                </pre>

                <h3>Cookies utilisés</h3>
                <p>aucun</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">connected</span>: État de la connexion, `true` si l'inscription est réussie.</li>
                </ul>
                <pre class="JSON">
                    {
                        "connected": true
                    }
                </pre>

                <h3>Cookies ajoutés</h3>
                <ul>
                    <li><strong>PHPSESSID</strong>: Cookie de session.</li>
                    <li><strong>login</strong>: Identifiant de l'utilisateur nouvellement inscrit.</li>
                </ul>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>201 Created : Inscription réussie, l'utilisateur est connecté.</li>
                    <li>400 Bad Request : Une ou plusieurs informations sont manquantes ou invalides.</li>
                    <li>409 Conflict : Le login ou l'adresse e-mail existe déjà.</li>
                    <li>405 Method Not Allowed : Méthode HTTP autre que POST utilisée.</li>
                    <li>500 Internal Server Error : Erreur interne de la base de données.</li>
                </ul>
            </div>
            
            <div class="endpoint">
                <h2>GET - Récupérer les informations de l'utilisateur</h2>
                <p><span class="method">GET</span> <code>/API/user.php?login={login}</code></p>
                <p>Permet de récupérer les informations d'un utilisateur connecté.</p>

                <h3>Paramètres URL</h3>
                <ul>
                    <li><span class="param">login</span> (requis) : Identifiant de l'utilisateur.</li>
                </ul>

                <h3>Exemple de requête</h3>
                <pre class="JSON">
                    GET /API/user.php?login=john_doe
                </pre>

                <h3>Conditions d'accès</h3>
                <p>L'utilisateur doit être connecté et le login doit correspondre à celui de la session active.</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">login</span>: Identifiant de l'utilisateur.</li>
                    <li><span class="res">nom</span>: Nom de l'utilisateur.</li>
                    <li><span class="res">prenom</span>: Prénom de l'utilisateur.</li>
                    <li><span class="res">date_de_naissance</span>: Date de naissance.</li>
                    <li><span class="res">mail</span>: Adresse e-mail.</li>
                    <li><span class="res">id_sexe</span>: Identifiant du sexe.</li>
                    <li><span class="res">id_age</span>: Tranche d'âge.</li>
                    <li><span class="res">id_pratique</span>: Niveau de pratique.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    {
                        "login": "john_doe",
                        "nom": "Doe",
                        "prenom": "John",
                        "date_de_naissance": "1990-01-01",
                        "mail": "john@example.com",
                        "id_sexe": 1,
                        "id_age": 2,
                        "id_pratique": 3
                    }
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Informations de l'utilisateur récupérées avec succès.</li>
                    <li>400 Bad Request : Paramètre login manquant.</li>
                    <li>401 Unauthorized : Utilisateur non connecté.</li>
                    <li>403 Forbidden : Accès interdit pour les informations d'un autre utilisateur.</li>
                    <li>404 Not Found : Utilisateur non trouvé.</li>
                    <li>500 Internal Server Error : Erreur de base de données.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>PUT - Mise à jour des informations de l'utilisateur</h2>
                <p><span class="method">PUT</span> <code>/API/user.php</code></p>
                <p>Permet de mettre à jour les informations de l'utilisateur connecté.</p>

                <h3>Paramètres JSON</h3>
                <ul>
                    <li><span class="param">login</span> (requis) : Identifiant de l'utilisateur.</li>
                    <li><span class="param">nom</span> (requis) : Nom de l'utilisateur.</li>
                    <li><span class="param">prenom</span> (requis) : Prénom de l'utilisateur.</li>
                    <li><span class="param">email</span> (requis) : Adresse e-mail de l'utilisateur.</li>
                    <li><span class="param">date</span> (requis) : Date de naissance (format YYYY-MM-DD).</li>
                    <li><span class="param">sexe</span> (requis) : Identifiant du sexe.</li>
                    <li><span class="param">niveauPratique</span> (requis) : Niveau de pratique.</li>
                </ul>

                <h3>Exemple de corps de requête</h3>
                <pre class="JSON">
                    {
                        "login": "john_doe",
                        "nom": "Doe",
                        "prenom": "John",
                        "email": "john@example.com",
                        "date": "1990-01-01",
                        "sexe": 1,
                        "niveauPratique": 3
                    }
                </pre>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">success</span>: Message confirmant la mise à jour.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    {
                        "success": "Utilisateur mis à jour avec succès."
                    }
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Mise à jour réussie.</li>
                    <li>400 Bad Request : Informations manquantes ou invalides.</li>
                    <li>404 Not Found : Utilisateur non trouvé.</li>
                    <li>500 Internal Server Error : Erreur de base de données.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>DELETE - Suppression de l'utilisateur</h2>
                <p><span class="method">DELETE</span> <code>/API/user.php</code></p>
                <p>Permet à l'utilisateur connecté de supprimer son compte.</p>

                <h3>Paramètres JSON</h3>
                <ul>
                    <li><span class="param">login</span> (requis) : Identifiant de l'utilisateur.</li>
                    <li><span class="param">motDePasse</span> (requis) : Mot de passe pour confirmation.</li>
                </ul>

                <h3>Exemple de corps de requête</h3>
                <pre class="JSON">
                    {
                        "login": "john_doe",
                        "motDePasse": "password123"
                    }
                </pre>

                <h3>Conditions d'accès</h3>
                <p>L'utilisateur doit être connecté et le login doit correspondre à celui de la session active.</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">success</span>: Message confirmant la suppression.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    {
                        "success": "Utilisateur supprimé avec succès."
                    }
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Suppression réussie.</li>
                    <li>400 Bad Request : Paramètres manquants ou invalides.</li>
                    <li>403 Forbidden : Accès interdit pour la suppression d'un autre utilisateur.</li>
                    <li>404 Not Found : Utilisateur non trouvé.</li>
                    <li>500 Internal Server Error : Erreur de base de données.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>POST - Déconnexion de l'utilisateur</h2>
                <p><span class="method">POST</span> <code>/API/disconnect.php</code></p>
                <p>Ce endpoint permet de déconnecter l'utilisateur en cours de session. Il supprime les données de session et les cookies associés à la connexion.</p>

                <h3>Paramètres</h3>
                <p>Aucun paramètre requis.</p>

                <h3>Exemple de requête</h3>
                <pre class="JSON">
                    POST /API/disconnect.php
                </pre>

                <h3>Fonctionnalité</h3>
                <p>Cette requête supprime toutes les données de session de l'utilisateur et supprime également le cookie de connexion. L'utilisateur est ensuite considéré comme déconnecté.</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">success</span>: Message confirmant la déconnexion.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    {
                        "success": "Utilisateur déconnecté avec succès."
                    }
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Déconnexion réussie.</li>
                    <li>405 Method Not Allowed : Méthode non autorisée (seule la méthode POST est acceptée).</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>GET - Récupération de la liste des aliments</h2>
                <p><span class="method">GET</span> <code>/API/aliments.php</code></p>
                <p>Ce endpoint permet de récupérer la liste complète des aliments, y compris leurs identifiants, labels et types d'aliments associés.</p>

                <h3>Paramètres</h3>
                <p>Aucun paramètre requis.</p>

                <h3>Exemple de requête</h3>
                <pre class="JSON">
                    GET /API/aliments.php
                </pre>

                <h3>Fonctionnalité</h3>
                <p>La requête retourne tous les aliments disponibles dans la base de données avec leurs informations respectives, y compris le type d'aliment associé. La base de données relie chaque aliment à un type d'aliment via une clé étrangère.</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">ID_ALIMENT</span> : Identifiant unique de l'aliment.</li>
                    <li><span class="res">LABEL_ALIMENT</span> : Nom ou label de l'aliment.</li>
                    <li><span class="res">LIBELE_TYPE</span> : Libellé du type d'aliment.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    [
                        {
                            "ID_ALIMENT": 1,
                            "LABEL_ALIMENT": "Pomme",
                            "LIBELE_TYPE": "Fruit"
                        },
                        {
                            "ID_ALIMENT": 2,
                            "LABEL_ALIMENT": "Poulet",
                            "LIBELE_TYPE": "Viande"
                        }
                        // Autres éléments...
                    ]
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Requête traitée avec succès, la liste des aliments est retournée.</li>
                    <li>500 Internal Server Error : Erreur lors de l'exécution de la requête SQL.</li>
                    <li>405 Method Not Allowed : Méthode non autorisée (seule la méthode GET est acceptée).</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>GET - Récupération des niveaux de pratique</h2>
                <p><span class="method">GET</span> <code>/API/niveaux_de_pratique.php</code></p>
                <p>Ce endpoint permet de récupérer la liste complète des niveaux de pratique disponibles dans la base de données, avec leurs identifiants et labels.</p>

                <h3>Paramètres</h3>
                <p>Aucun paramètre requis.</p>

                <h3>Exemple de requête</h3>
                <pre class="JSON">
                    GET /API/niveaux_de_pratique.php
                </pre>

                <h3>Fonctionnalité</h3>
                <p>La requête retourne tous les niveaux de pratique disponibles dans la base de données, y compris l'identifiant de chaque niveau et son libellé associé.</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">ID_PRATIQUE</span> : Identifiant unique du niveau de pratique.</li>
                    <li><span class="res">LIBELE_PRATIQUE</span> : Libellé du niveau de pratique.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    [
                        {
                            "ID_PRATIQUE": 1,
                            "LIBELE_PRATIQUE": "Débutant"
                        },
                        {
                            "ID_PRATIQUE": 2,
                            "LIBELE_PRATIQUE": "Intermédiaire"
                        },
                        {
                            "ID_PRATIQUE": 3,
                            "LIBELE_PRATIQUE": "Avancé"
                        }
                        // Autres niveaux de pratique...
                    ]
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Requête traitée avec succès, la liste des niveaux de pratique est retournée.</li>
                    <li>500 Internal Server Error : Erreur lors de l'exécution de la requête SQL.</li>
                    <li>405 Method Not Allowed : Méthode non autorisée (seule la méthode GET est acceptée).</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>GET - Récupération des sexes</h2>
                <p><span class="method">GET</span> <code>/API/sexes.php</code></p>
                <p>Ce endpoint permet de récupérer la liste complète des sexes disponibles dans la base de données, avec leurs identifiants et labels.</p>

                <h3>Paramètres</h3>
                <p>Aucun paramètre requis.</p>

                <h3>Exemple de requête</h3>
                <pre class="JSON">
                    GET /API/sexes.php
                </pre>

                <h3>Fonctionnalité</h3>
                <p>La requête retourne tous les sexes disponibles dans la base de données, y compris l'identifiant de chaque sexe et son libellé associé.</p>

                <h3>Réponse JSON</h3>
                <ul>
                    <li><span class="res">ID_SEXE</span> : Identifiant unique du sexe.</li>
                    <li><span class="res">LIBELE_SEXE</span> : Libellé du sexe.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    [
                        {
                            "ID_SEXE": 1,
                            "LIBELE_SEXE": "Homme"
                        },
                        {
                            "ID_SEXE": 2,
                            "LIBELE_SEXE": "Femme"
                        },
                        {
                            "ID_SEXE": 3,
                            "LIBELE_SEXE": "Autre"
                        }
                        // Autres sexes...
                    ]
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Requête traitée avec succès, la liste des sexes est retournée.</li>
                    <li>500 Internal Server Error : Erreur lors de l'exécution de la requête SQL.</li>
                    <li>405 Method Not Allowed : Méthode non autorisée (seule la méthode GET est acceptée).</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>GET - Récupération des nutriments consommés et des besoins</h2>
                <p><span class="method">GET</span> <code>/API/nutrients.php</code></p>
                <p>Ce endpoint permet de récupérer les nutriments consommés par un utilisateur, ainsi que ses besoins nutritionnels pour une date donnée. Les résultats incluent un pourcentage de consommation par rapport aux besoins.</p>

                <h3>Paramètres</h3>
                <p>Les paramètres suivants sont requis dans la requête GET :</p>
                <ul>
                    <li><span class="param">login</span> : Le nom d'utilisateur (login) de l'utilisateur.</li>
                    <li><span class="param">date</span> : La date pour laquelle on souhaite récupérer les informations (au format YYYY-MM-DD).</li>
                </ul>

                <h3>Exemple de requête</h3>
                <pre class="JSON">
                    GET /API/nutrients.php?login=john.doe&date=2024-11-06
                </pre>

                <h3>Fonctionnalité</h3>
                <p>Ce endpoint vérifie si l'utilisateur est connecté et si les paramètres nécessaires (login et date) sont fournis. Si les informations sont valides, il renvoie un pourcentage de la consommation des nutriments de l'utilisateur pour cette date par rapport à ses besoins.</p>

                <h3>Réponse JSON</h3>
                <p>La réponse contient une liste de nutriments consommés, associée à un pourcentage de consommation par rapport aux besoins de l'utilisateur.</p>
                <ul>
                    <li><span class="res">LIBELE_NUTRIMENT</span> : Le nom du nutriment.</li>
                    <li><span class="res">pourcentage</span> : Le pourcentage de consommation du nutriment par rapport aux besoins de l'utilisateur.</li>
                </ul>

                <h3>Exemple de réponse</h3>
                <pre class="JSON">
                    {
                        "Protéines": 75,
                        "Glucides": 50,
                        "Lipides": 60,
                        // Autres nutriments...
                    }
                </pre>

                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Requête traitée avec succès, données renvoyées.</li>
                    <li>400 Bad Request : Les paramètres 'login' et 'date' sont requis.</li>
                    <li>401 Unauthorized : L'utilisateur doit être connecté pour accéder à cette ressource.</li>
                    <li>403 Forbidden : L'utilisateur tente d'accéder aux informations d'un autre utilisateur.</li>
                    <li>404 Not Found : L'utilisateur n'a pas été trouvé dans la base de données.</li>
                    <li>500 Internal Server Error : Erreur lors de l'exécution de la requête SQL.</li>
                    <li>405 Method Not Allowed : Méthode non autorisée (seule la méthode GET est acceptée).</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>GET - Récupération des repas</h2>
                <p><span class="method">GET</span> <code>/API/repas.php</code></p>
                <p>Récupère la liste des repas pour un utilisateur sur une période spécifiée (jour, semaine, ou mois).</p>
                
                <h4>Paramètres requis</h4>
                <ul>
                    <li><span class="param">login</span> : Le login de l'utilisateur (obligatoire).</li>
                    <li><span class="param">interval</span> : Intervalle de temps pour filtrer les repas. Options possibles :
                        <ul>
                            <li><code>day</code> : pour filtrer par date spécifique.</li>
                            <li><code>week</code> (par défaut) : pour récupérer les repas de la semaine en cours.</li>
                            <li><code>month</code> : pour récupérer les repas d'un mois spécifique.</li>
                        </ul>
                    </li>
                    <li><span class="param">date</span> : Date (format <code>YYYY-MM-DD</code>) si l'intervalle est <code>day</code> ou <code>month</code>.</li>
                </ul>

                <h4>Exemple de requête</h4>
                <pre class="JSON">
                    GET /API/repas.php?login=john.doe&interval=week
                </pre>

                <h4>Réponse JSON</h4>
                <p>La réponse contient une liste des repas avec leurs détails :</p>
                <pre class="JSON">
                [
                    {
                        "ID_REPAS": 1,
                        "QUANTITE": 2,
                        "DATE": "2024-11-06",
                        "LABEL_ALIMENT": "Salade"
                    },
                    {
                        "ID_REPAS": 2,
                        "QUANTITE": 1,
                        "DATE": "2024-11-06",
                        "LABEL_ALIMENT": "Pâtes"
                    }
                ]
                </pre>
            </div>

            <div class="endpoint">
                <h2>POST - Créer un repas</h2>
                <p><span class="method">POST</span> <code>/API/repas.php</code></p>
                <p>Permet de créer un nouveau repas pour l'utilisateur connecté.</p>
                
                <h4>Paramètres requis</h4>
                <ul>
                    <li><span class="param">login</span> : Le login de l'utilisateur (obligatoire).</li>
                    <li><span class="param">quantite</span> : La quantité d'aliment consommée (obligatoire).</li>
                    <li><span class="param">date</span> : La date du repas (format <code>YYYY-MM-DD</code>, obligatoire).</li>
                    <li><span class="param">id_aliment</span> : L'ID de l'aliment consommé (obligatoire).</li>
                </ul>

                <h4>Exemple de requête</h4>
                <pre class="JSON">
                    {
                        "login": "john.doe",
                        "quantite": 2,
                        "date": "2024-11-06",
                        "id_aliment": 1
                    }
                </pre>

                <h4>Réponse JSON</h4>
                <pre class="JSON">
                {
                    "message": "Repas créé avec succès."
                }
                </pre>
            </div>

            <div class="endpoint">
                <h2>PUT - Mettre à jour un repas</h2>
                <p><span class="method">PUT</span> <code>/API/repas.php</code></p>
                <p>Permet de mettre à jour un repas existant de l'utilisateur connecté.</p>
                
                <h4>Paramètres requis</h4>
                <ul>
                    <li><span class="param">login</span> : Le login de l'utilisateur (obligatoire).</li>
                    <li><span class="param">ID_REPAS</span> : L'ID du repas à mettre à jour (obligatoire).</li>
                    <li><span class="param">quantite</span> : La quantité d'aliment mise à jour (obligatoire).</li>
                    <li><span class="param">date</span> : La nouvelle date du repas (format <code>YYYY-MM-DD</code>, obligatoire).</li>
                    <li><span class="param">id_aliment</span> : L'ID de l'aliment (optionnel, si vous voulez modifier l'aliment).</li>
                </ul>

                <h4>Exemple de requête</h4>
                <pre class="JSON">
                    {
                        "login": "john.doe",
                        "ID_REPAS": 1,
                        "quantite": 3,
                        "date": "2024-11-06",
                        "id_aliment": 2
                    }
                </pre>

                <h4>Réponse JSON</h4>
                <pre class="JSON">
                {
                    "message": "Repas mis à jour avec succès."
                }
                </pre>
            </div>

            <div class="endpoint">
                <h2>DELETE - Supprimer un repas</h2>
                <p><span class="method">DELETE</span> <code>/API/repas.php</code></p>
                <p>Permet de supprimer un repas existant de l'utilisateur connecté.</p>
                
                <h4>Paramètres requis</h4>
                <ul>
                    <li><span class="param">login</span> : Le login de l'utilisateur (obligatoire).</li>
                    <li><span class="param">ID_REPAS</span> : L'ID du repas à supprimer (obligatoire).</li>
                </ul>

                <h4>Exemple de requête</h4>
                <pre class="JSON">
                    {
                        "login": "john.doe",
                        "ID_REPAS": 1
                    }
                </pre>

                <h4>Réponse JSON</h4>
                <pre class="JSON">
                {
                    "message": "Repas supprimé avec succès."
                }
                </pre>
            </div>

            <div class="endpoint">
                <h2>GET - Récupérer les recommandations nutritionnelles</h2>
                <p><span class="method">GET</span> <code>/API/recommandations.php</code></p>
                <p>Permet de récupérer les recommandations nutritionnelles personnalisées pour l'utilisateur connecté en fonction de son sexe, âge et niveau d'activité.</p>

                <h4>Paramètres requis</h4>
                <ul>
                    <li><span class="param">login</span> : Le login de l'utilisateur (obligatoire).</li>
                </ul>

                <h4>Exemple de requête</h4>
                <pre class="JSON">
            {
                "login": "john.doe"
            }
                </pre>

                <h4>Réponse JSON</h4>
                <pre class="JSON">
            {
                "eau": 2.3,
                "energie": 2500,
                "proteines": 30,
                "glucides": 343.75,
                "sel": 5
            }
                </pre>
            </div>


    </body>
</html>