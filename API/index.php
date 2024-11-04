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
            .response {
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
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Documentation API</h1>

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
                <div class="response">
                    {
                        "login": "john_doe",
                        "motDePasse": "password123"
                    }
                </div>
                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Connexion réussie.</li>
                    <li>400 Bad Request : Le login ou le mot de passe est manquant.</li>
                    <li>401 Unauthorized : Identifiants incorrects.</li>
                    <li>405 Method Not Allowed : Méthode HTTP autre que POST utilisée.</li>
                    <li>500 Internal Server Error : Erreur interne de la base de données.</li>
                </ul>
                <h3>Exemple de réponse</h3>
                <div class="response">
                    {
                        "connected": true
                    }
                </div>
            </div>

            <div class="endpoint">
                <h2>POST - Créer un nouvel utilisateur</h2>
                <p><span class="method">POST</span> <code>/API/create_user</code></p>
                <p>Permet de créer un nouvel utilisateur dans la base de données.</p>
                <h3>Paramètres JSON</h3>
                <ul>
                    <li><span class="param">login</span> (requis) : Identifiant choisi par l'utilisateur.</li>
                    <li><span class="param">motDePasse</span> (requis) : Mot de passe de l'utilisateur.</li>
                    <li><span class="param">email</span> (requis) : Adresse e-mail de l'utilisateur.</li>
                    <li><span class="param">nom</span> (requis) : Nom de famille de l'utilisateur.</li>
                    <li><span class="param">prenom</span> (requis) : Prénom de l'utilisateur.</li>
                    <li><span class="param">sexe</span> (requis) : Identifiant du sexe de l'utilisateur.</li>
                    <li><span class="param">niveauPratique</span> (requis) : Identifiant du niveau de pratique de l'utilisateur.</li>
                    <li><span class="param">date</span> (requis) : Date de naissance de l'utilisateur au format <code>YYYY-MM-DD</code>.</li>
                </ul>
                <h3>Exemple de corps de requête</h3>
                <div class="response">
                    {
                        "login": "john_doe",
                        "motDePasse": "password123",
                        "email": "john.doe@example.com",
                        "nom": "Doe",
                        "prenom": "John",
                        "sexe": 1,
                        "niveauPratique": 2,
                        "date": "1990-01-01"
                    }
                </div>
                <h3>Codes de réponse</h3>
                <ul>
                    <li>201 Created : Utilisateur créé avec succès.</li>
                    <li>400 Bad Request : Informations manquantes ou invalides.</li>
                    <li>409 Conflict : <code>login</code> ou <code>email</code> déjà utilisés.</li>
                    <li>405 Method Not Allowed : Méthode HTTP autre que POST utilisée.</li>
                    <li>500 Internal Server Error : Erreur interne de la base de données.</li>
                </ul>
                <h3>Exemple de réponse</h3>
                <div class="response">
                    {
                        "connected": true
                    }
                </div>
            </div>

            <div class="endpoint">
                <h2>POST - Déconnexion de l'utilisateur</h2>
                <p><span class="method">POST</span> <code>/API/disconnect.php</code></p>
                <p>Permet de déconnecter l'utilisateur actuellement connecté.</p>
                
                <h3>Entrées</h3>
                <p>Aucune donnée n'est requise dans le corps de la requête. La requête doit être effectuée par un utilisateur actuellement connecté (session active).</p>
                
                <h3>Codes de réponse</h3>
                <ul>
                    <li>200 OK : Déconnexion réussie.</li>
                    <li>405 Method Not Allowed : La méthode HTTP utilisée n'est pas autorisée.</li>
                </ul>
                
                <h3>Données de réponse</h3>
                <div class="response">
                    {
                        "success": "Utilisateur déconnecté avec succès."
                    }
                </div>
                
                <h3>Cookies manipulés</h3>
                <p>Le cookie <code>login</code> est supprimé en le marquant comme expiré, supprimant toutes les traces de la session de l’utilisateur.</p>

                <h3>Cas d'utilisation</h3>
                <p>Cet endpoint est utilisé pour déconnecter l'utilisateur en cours de session, en vidant la session et en supprimant le cookie login.</p>
                
                <h3>Remarques supplémentaires</h3>
                <ul>
                    <li>Sécurité : Assure que tous les éléments de session sont bien détruits, évitant ainsi toute trace de la session précédente.</li>
                    <li>Extensibilité : D'autres cookies de connexion peuvent être ajoutés pour suppression si nécessaire.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>POST - Rapport de bug</h2>
                <p><span class="method">POST</span> <code>/API/bugReport.php</code></p>
                <p>Permet aux utilisateurs d'envoyer des rapports de bug ou des messages de contact directement par e-mail à l'administrateur ou au support technique.</p>

                <h3>Entrées</h3>
                <p>Format : JSON</p>
                <h4>Champs requis :</h4>
                <ul>
                    <li><span class="param">name</span> (requis) : Nom de l’utilisateur (type string).</li>
                    <li><span class="param">email</span> (requis) : Adresse e-mail de l’utilisateur (type string).</li>
                    <li><span class="param">subject</span> (requis) : Sujet du message de bug (type string).</li>
                    <li><span class="param">message</span> (requis) : Détails du bug (type string).</li>
                </ul>

                <h3>Sorties</h3>
                <p>Format de la réponse : JSON</p>
                <h4>Codes de réponse HTTP :</h4>
                <ul>
                    <li>200 OK : L'email de rapport de bug a été envoyé avec succès.</li>
                    <li>400 Bad Request : Données du formulaire incomplètes.</li>
                    <li>500 Internal Server Error : Échec de l'envoi de l'email.</li>
                    <li>405 Method Not Allowed : Méthode HTTP non autorisée.</li>
                </ul>
                
                <h4>Données de réponse :</h4>
                <div class="response">
                    {
                        "success": "Message envoyé avec succès",
                        "error": "Message d'erreur si l'envoi de l'email échoue"
                    }
                </div>

                <h3>Cookies manipulés</h3>
                <p>Aucune manipulation de cookies n'est effectuée dans cet endpoint.</p>

                <h3>Cas d'utilisation (Use Case)</h3>
                <p>Cet endpoint est utilisé pour que les utilisateurs puissent envoyer des rapports de bug ou des messages de contact directement par e-mail à l'administrateur ou au support technique.</p>

                <h3>Remarques supplémentaires</h3>
                <ul>
                    <li><span class="param">Protection XSS</span> : Tous les champs sont nettoyés via <code>htmlspecialchars()</code> pour prévenir toute injection HTML.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>GET - Niveaux de pratique</h2>
                <p><span class="method">GET</span> <code>/API/niveaux_de_pratique.php</code></p>
                <p>Cet endpoint permet de récupérer tous les niveaux de pratique présents dans la table <code>NIVEAU_DE_PRATIQUE</code>.</p>

                <h3>Entrées</h3>
                <p>Aucune entrée n'est requise sous forme de paramètres ou de données envoyées.</p>

                <h3>Sorties</h3>
                <p>Format de la réponse : JSON</p>
                <h4>Structure de la réponse :</h4>
                <p>Tableau d'objets contenant les champs suivants pour chaque niveau de pratique :</p>
                <ul>
                    <li><span class="param">ID_PRATIQUE</span> : Identifiant unique du niveau de pratique (type int).</li>
                    <li><span class="param">LIBELE_PRATIQUE</span> : Libellé descriptif du niveau de pratique (type string).</li>
                </ul>
                
                <h4>Codes de réponse HTTP :</h4>
                <ul>
                    <li>200 OK : Requête réussie, retourne les niveaux de pratique.</li>
                    <li>500 Internal Server Error : Erreur lors de l’exécution de la requête.</li>
                    <li>405 Method Not Allowed : Méthode HTTP autre que GET non autorisée.</li>
                </ul>

                <h3>Cookies manipulés</h3>
                <p>Aucun cookie n'est créé, lu ou supprimé par cet endpoint.</p>

                <h3>Cas d'utilisation (Use Case)</h3>
                <p>Cet endpoint est utilisé pour récupérer la liste des niveaux de pratique disponibles dans le système. Il est utile pour préremplir une liste déroulante dans un formulaire d'inscription ou de profil utilisateur.</p>

                <h3>Remarques supplémentaires</h3>
                <p><span class="param">Gestion d'erreurs</span> : En cas d'échec de la requête SQL, un code d'erreur 500 est renvoyé avec un message détaillant l'erreur.</p>
            </div>

            <div class="endpoint">
                <h2>API - Gestion des Repas</h2>
                <h3>Description Générale</h3>
                <p>Cet endpoint permet aux utilisateurs connectés de gérer leurs repas sur la plateforme. Les actions possibles sont :</p>
                <ul>
                    <li><span class="method">GET</span> : Récupérer les repas enregistrés sur une période de 7 jours.</li>
                    <li><span class="method">POST</span> : Enregistrer un nouveau repas.</li>
                    <li><span class="method">PUT</span> : Modifier un repas existant.</li>
                    <li><span class="method">DELETE</span> : Supprimer un repas.</li>
                </ul>

                <h3>Détails Techniques</h3>
                <h4>URL et Méthodes Disponibles</h4>
                <p><strong>URL</strong> : <code>/API/repas.php</code></p>
                <p><strong>Méthodes HTTP supportées</strong> : <span class="method">GET</span>, <span class="method">POST</span>, <span class="method">PUT</span>, <span class="method">DELETE</span></p>

                <h4>Authentification</h4>
                <p>L'utilisateur doit être authentifié et la session doit inclure les champs suivants :</p>

                <h3>Documentation par Méthode</h3>

                <h4>1. <span class="method">GET</span> (Récupérer les repas)</h4>
                <p><strong>Paramètre requis</strong> : <span class="param">login</span> (doit correspondre au login de la session)</p>
                <p><strong>Réponse</strong> : Un tableau de repas des 7 derniers jours, chaque repas incluant les champs suivants :</p>
                <ul>
                    <li><span class="param">ID_REPAS</span> : Identifiant unique du repas.</li>
                    <li><span class="param">QUANTITE</span> : Quantité d’aliments consommés.</li>
                    <li><span class="param">DATE</span> : Date du repas.</li>
                    <li><span class="param">LABEL_ALIMENT</span> : Nom de l’aliment.</li>
                </ul>
                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Succès, retourne les repas.</li>
                    <li>400 Bad Request : Le paramètre login est manquant.</li>
                    <li>401 Unauthorized : Le login fourni ne correspond pas à l'utilisateur connecté.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>

                <h4>2. <span class="method">POST</span> (Créer un nouveau repas)</h4>
                <p><strong>Corps JSON requis</strong> :</p>
                <ul>
                    <li><span class="param">login</span> (string) : Identifiant de l'utilisateur connecté.</li>
                    <li><span class="param">quantite</span> (float) : Quantité d'aliment.</li>
                    <li><span class="param">date</span> (date) : Date du repas.</li>
                    <li><span class="param">id_aliment</span> (int) : Identifiant de l’aliment.</li>
                </ul>
                <h5>Codes de réponse :</h5>
                <ul>
                    <li>201 Created : Repas créé avec succès.</li>
                    <li>400 Bad Request : Champs manquants ou aliment inexistant.</li>
                    <li>401 Unauthorized : Le login ne correspond pas à l'utilisateur connecté.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>

                <h4>3. <span class="method">PUT</span> (Mettre à jour un repas)</h4>
                <p><strong>Corps JSON requis</strong> :</p>
                <ul>
                    <li><span class="param">login</span> (string) : Identifiant de l'utilisateur connecté.</li>
                    <li><span class="param">id_REPAS</span> (int) : Identifiant du repas.</li>
                    <li><span class="param">quantite</span> (float) : Nouvelle quantité.</li>
                    <li><span class="param">date</span> (date) : Nouvelle date du repas.</li>
                    <li><span class="param">id_aliment</span> (int) : Identifiant de l'aliment (optionnel).</li>
                </ul>
                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Mise à jour réussie.</li>
                    <li>400 Bad Request : Champs manquants ou aliment inexistant.</li>
                    <li>401 Unauthorized : Le login ne correspond pas à l'utilisateur connecté.</li>
                    <li>404 Not Found : Repas introuvable ou accès non autorisé.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>

                <h4>4. <span class="method">DELETE</span> (Supprimer un repas)</h4>
                <p><strong>Corps JSON requis</strong> :</p>
                <ul>
                    <li><span class="param">login</span> (string) : Identifiant de l'utilisateur connecté.</li>
                    <li><span class="param">id_REPAS</span> (int) : Identifiant du repas à supprimer.</li>
                </ul>
                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Suppression réussie.</li>
                    <li>400 Bad Request : Champs manquants.</li>
                    <li>401 Unauthorized : Le login ne correspond pas à l'utilisateur connecté.</li>
                    <li>404 Not Found : Repas introuvable ou accès non autorisé.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>API/niveaux_de_pratique.php</h2>
                <h3>1. URL et Méthode</h3>
                <p><strong>URL</strong> : <code>/API/niveaux_de_pratique.php</code></p>
                <p><strong>Méthode HTTP</strong> : <span class="method">GET</span></p>

                <h3>2. Entrées (Input)</h3>
                <p><strong>Requête</strong> : Cette API n'a besoin d'aucune entrée sous forme de paramètres ou de données envoyées.</p>
                <p><strong>Conditions d’utilisation</strong> : Une requête GET sur cet endpoint renvoie tous les niveaux de pratique présents dans la table NIVEAU_DE_PRATIQUE.</p>

                <h3>3. Sorties (Output)</h3>
                <p><strong>Format de la réponse</strong> : JSON</p>
                <p><strong>Structure de la réponse</strong> :</p>
                <ul>
                    <li><strong>Tableau d'objets contenant les champs suivants pour chaque niveau de pratique :</strong></li>
                    <li><span class="param">ID_PRATIQUE</span> : Identifiant unique du niveau de pratique (type int).</li>
                    <li><span class="param">LIBELE_PRATIQUE</span> : Libellé descriptif du niveau de pratique (type string).</li>
                </ul>
                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Requête réussie, retourne les niveaux de pratique.</li>
                    <li>500 Internal Server Error : Erreur lors de l’exécution de la requête.</li>
                    <li>405 Method Not Allowed : Méthode HTTP autre que GET non autorisée.</li>
                </ul>

                <h3>4. Cookies manipulés</h3>
                <p>Aucun cookie n'est créé, lu ou supprimé par cet endpoint.</p>

                <h3>5. Cas d'utilisation (Use Case)</h3>
                <p>Cet endpoint est utilisé pour récupérer la liste des niveaux de pratique disponibles dans le système. Par exemple, il est utile pour préremplir une liste déroulante dans un formulaire d'inscription ou de profil utilisateur.</p>

                <h3>6. Remarques supplémentaires</h3>
                <p><strong>Gestion d'erreurs</strong> : En cas d'échec de la requête SQL, un code d'erreur 500 est renvoyé avec un message détaillant l'erreur.</p>
            </div>

            <div class="endpoint">
                <h2>API/sexes.php</h2>
                <h3>Description Générale</h3>
                <p>Cet endpoint permet de récupérer la liste des sexes disponibles dans la base de données, comme par exemple : "Homme", "Femme", etc.</p>

                <h3>Détails Techniques</h3>
                <h4>URL et Méthodes Disponibles</h4>
                <p><strong>URL</strong> : <code>/API/sexes.php</code></p>
                <p><strong>Méthodes HTTP supportées</strong> : <span class="method">GET</span></p>

                <h4>Authentification</h4>
                <p>Aucune authentification n'est requise pour accéder à cet endpoint.</p>

                <h3>Documentation par Méthode</h3>

                <h4>1. <span class="method">GET</span> (Récupérer tous les sexes)</h4>
                <p><strong>Description</strong> : Récupère la liste de tous les sexes disponibles.</p>

                <h5>Exemple de requête :</h5>
                <pre><code>GET /API/sexes.php</code></pre>

                <h5>Réponse</h5>
                <p>Un tableau JSON contenant tous les sexes, avec chaque entrée ayant les champs suivants :</p>
                <ul>
                    <li><span class="param">ID_SEXE</span> : Identifiant unique du sexe.</li>
                    <li><span class="param">LIBELE_SEXE</span> : Nom ou libellé du sexe.</li>
                </ul>

                <h5>Exemple de réponse :</h5>
                <div class="response">
                    [
                    {"ID_SEXE": 1, "LIBELE_SEXE": "Homme"},
                    {"ID_SEXE": 2, "LIBELE_SEXE": "Femme"}
                    ]
                </div>

                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Succès, retourne les sexes.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>
            </div>

            <div class="endpoint">
                <h2>API/user.php</h2>
                <h3>Description</h3>
                <p>Cette API permet de gérer les informations des utilisateurs, en effectuant des opérations de lecture, mise à jour et suppression. Les utilisateurs doivent être authentifiés pour accéder aux différentes fonctionnalités.</p>

                <h3>Détails Techniques</h3>
                <h4>URL et Méthodes Disponibles</h4>
                <p><strong>URL</strong> : <code>/API/user.php</code></p>
                <p><strong>Méthodes HTTP supportées</strong> : <span class="method">GET</span>, <span class="method">PUT</span>, <span class="method">DELETE</span></p>

                <h4>Authentification</h4>
                <p>Les utilisateurs doivent être connectés (via une session) pour accéder aux fonctionnalités de cet endpoint.</p>
                <p>Si l’utilisateur n'est pas authentifié, une erreur 401 Unauthorized sera renvoyée.</p>

                <h3>Documentation par Méthode</h3>

                <h4>1. <span class="method">GET</span> (Récupérer les informations utilisateur)</h4>
                <p><strong>Description</strong> : Récupère les informations de l'utilisateur connecté.</p>

                <h5>Requête :</h5>
                <pre><code>GET /API/user.php?login={login}</code></pre>

                <h5>Paramètre :</h5>
                <ul>
                    <li><span class="param">login</span> (requis) : Le login de l’utilisateur. L’utilisateur ne peut récupérer que ses propres informations.</li>
                </ul>

                <h5>Exemple de réponse :</h5>
                <div class="response">
                    [
                        {
                            "LOGIN": "john_doe",
                            "NOM": "Doe",
                            "PRENOM": "John",
                            "DATE_DE_NAISSANCE": "1990-01-01",
                            "MAIL": "john.doe@example.com",
                            "ID_SEXE": 1,
                            "ID_AGE": 3,
                            "ID_PRATIQUE": 2
                        }
                    ]
                </div>

                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Succès, retourne les informations de l’utilisateur.</li>
                    <li>403 Forbidden : L’utilisateur tente d’accéder aux informations d’un autre utilisateur.</li>
                    <li>404 Not Found : Utilisateur non trouvé.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>


                <h4>2. <span class="method">PUT</span> (Mise à jour des informations utilisateur)</h4>
                <p><strong>Description</strong> : Met à jour les informations de l’utilisateur connecté.</p>

                <h5>Requête :</h5>
                <pre><code>PUT /API/user.php</code></pre>

                <h5>Paramètres JSON :</h5>
                <ul>
                    <li><span class="param">login</span> (requis) : Login de l’utilisateur (doit correspondre à l'utilisateur connecté).</li>
                    <li><span class="param">nom</span>, <span class="param">prenom</span>, <span class="param">email</span>, <span class="param">date</span>, <span class="param">sexe</span>, <span class="param">niveauPratique</span> (tous requis) : Détails mis à jour de l’utilisateur.</li>
                </ul>

                <h5>Exemple de corps de requête :</h5>
                <div class="response">
            {
                "login": "john_doe",
                "nom": "Doe",
                "prenom": "John",
                "email": "john.doe@example.com",
                "date": "1990-01-01",
                "sexe": 1,
                "niveauPratique": 2
            }
                </div>

                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Succès, informations mises à jour.</li>
                    <li>400 Bad Request : Données incomplètes ou invalides.</li>
                    <li>404 Not Found : Utilisateur non trouvé.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>

                <h4>3. <span class="method">DELETE</span> (Supprimer l’utilisateur)</h4>
                <p><strong>Description</strong> : Supprime l’utilisateur connecté.</p>

                <h5>Requête :</h5>
                <pre><code>DELETE /API/user.php</code></pre>

                <h5>Paramètres JSON :</h5>
                <ul>
                    <li><span class="param">login</span> (requis) : Login de l’utilisateur.</li>
                    <li><span class="param">motDePasse</span> (requis) : Mot de passe de l’utilisateur pour validation.</li>
                </ul>

                <h5>Exemple de corps de requête :</h5>
                <div class="response">
            {
                "login": "john_doe",
                "motDePasse": "password123"
            }
                </div>

                <h5>Codes de réponse :</h5>
                <ul>
                    <li>200 OK : Succès, utilisateur supprimé.</li>
                    <li>403 Forbidden : L’utilisateur tente de supprimer un autre utilisateur ou mot de passe incorrect.</li>
                    <li>404 Not Found : Utilisateur non trouvé.</li>
                    <li>500 Internal Server Error : Problème d'exécution de la requête SQL.</li>
                </ul>
            </div>
        </div>
    </body>
</html>