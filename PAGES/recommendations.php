<section>
    <h1>Recommandations Nutritionnelles</h1>
    
    <form id="userForm">
        <label for="login">Login :</label>
        <input type="text" id="login" required>
        <button type="submit">Obtenir les recommandations</button>
    </form>

    <div id="recommandations" style="display:none;">
        <div class="recommandation">Eau : <span id="eau" class="indicateur"></span> L</div>
        <div class="recommandation">Énergie : <span id="energie" class="indicateur"></span> kcal</div>
        <div class="recommandation">Protéines : <span id="proteines" class="indicateur"></span> g</div>
        <div class="recommandation">Glucides : <span id="glucides" class="indicateur"></span> g</div>
        <div class="recommandation">Sel : <span id="sel" class="indicateur"></span> g</div>
    </div>

    <script>
        // Fonction pour récupérer et afficher les recommandations
        async function afficherRecommandations(login) {
            try {
                const response = await fetch(`http://localhost/Projet_IMangerMieux/Projet_IMangerMieux/API/recommandations.php?login=${login}`, {
                    method: 'GET',
                    credentials: 'include' // Pour inclure les cookies de session
                });
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP ! statut : ${response.status}`);
                }

                const data = await response.json();

                if (data.error) {
                    console.error('Erreur:', data.error);
                    alert(data.error); // Affiche l'erreur à l'utilisateur
                    return;
                }

                // Afficher les recommandations
                document.getElementById('eau').innerText = data.eau.toFixed(2);
                document.getElementById('energie').innerText = data.energie.toFixed(0);
                document.getElementById('proteines').innerText = data.proteines.toFixed(1);
                document.getElementById('glucides').innerText = data.glucides.toFixed(1);
                document.getElementById('sel').innerText = data.sel.toFixed(0); // Correction ici pour le sel
                // Afficher la section des recommandations
                document.getElementById('recommandations').style.display = 'block';
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue, veuillez réessayer.');
            }
        }

        // Gestion de l'envoi du formulaire
        document.getElementById('userForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche le rechargement de la page
            const login = document.getElementById('login').value;
            afficherRecommandations(login); // Appeler la fonction avec le login
        });
    </script>
</section>
