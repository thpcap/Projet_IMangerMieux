<section id="content">
    <h1 style="color: blue; text-align: center">Recommandations Nutritionnelles</h1>
    <P style="text-align: justify;">Chaque individu a des besoins nutritionnels spécifiques qui varient selon 
        l'âge, le sexe, le niveau d'activité physique et d'autres facteurs. Pour assurer une bonne santé, 
        il est essentiel de respecter certaines recommandations de consommation en <strong>Energie</strong>, macronutriments 
        (<strong>Protéines, Glucides, Lipides</strong>) et micronutriments (<strong>Vitamines, Minéraux, etc.</strong> ). Ces recommandations 
        sont définies par l'Organisation Mondiale de la Santé OMS et servent de repères pour une alimentation équilibrée.</P>
    <h2>Calculez votre besoin nutritionnel par jour</h2>
    <p style="text-align: justify;"> <strong>BesoinsPro©</strong> est un outil de calcul des besoins nutritionnels 
        vous permet d'estimer votre apport quotidien en nutriments essentiels. Il s'appuie sur 
        des méthodes utilisées par les diététiciens-nutritionnistes pour fournir des recommandations 
        basées sur votre âge, sexe, poids, taille et niveau d'activité physique.</p>
    
    <div id="inputFields" style="margin: auto; margin-top: 20px; max-width: 800px; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); background-color: #f9f9f9;">
        <h2 style="text-align: center; color: #333;">BesoinsPro©</h2>
        <label for="nutrientSelect" style="font-weight: bold;">Sélectionnez un nutriment :</label>
        <select id="nutrientSelect" style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">--Sélectionnez--</option>
            <option value="eau">Eau</option>
            <option value="energie">Énergie</option>
            <option value="proteines">Protéines</option>
            <option value="glucides">Glucides</option>
            <option value="sel">Sel</option>
        </select>

    
        <!-- Les champs d'entrées sont affichés par défaut -->
        <label for="vous êtes" style="font-weight: bold;">Vous êtes :</label>
        <select id="sexe"style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="homme">Homme</option>
            <option value="femme">Femme</option>
            <option value="autre">Autre</option>
        </select>
    
    
        <label for="poids" style="font-weight: bold;">Votre poids (kg) :</label>
        <input style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;" type="number" id="poids" required><br>
    
        <label for="taille"style="font-weight: bold;">Votre taille (cm) :</label>
        <input style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;" type="number" id="taille" required><br>
        
        <label for="age"style="font-weight: bold;">Votre âge :</label>
        <input style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;" type="number" id="age" required><br>
        <label for="niveauActivite"style="font-weight: bold;">Niveau d'activité physique :</label>
        <select id="niveauActivite" style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="1">Bas</option>
            <option value="2">Moyen</option>
            <option value="3">Elevé</option>
        </select>
    

        <button id="calculateBtn" style="background-color: #007BFF; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" onclick="calculateNeeds()">Mes besoins</button>

        <div id="result" style="margin-top: 15px; display: none;">
            <h3>Résultat :</h3>
            <div id="output"></div>
        </div>
    </div>
    <script>
        function displayResults(data) {
            // Crée un conteneur vide pour les résultats
            let outputHtml = '<ul>';
            // Définition des unités pour chaque type de résultat
            const units = {
                eau: 'L/j',
                energie: 'kcal/j',
                proteines: 'g/j',
                glucides: 'g/j',
                sel: 'g/j'
            };
            // Parcourt chaque clé-valeur des résultats
            for (const [key, value] of Object.entries(data)) {
                const roundedValue = typeof value === 'number' ? value.toFixed(2) : value;
                const unit = units[key] ? ` ${units[key]}` : ''; // Ajoute l'unité si elle existe
                outputHtml += `<li><strong>${key}:</strong> ${roundedValue}</li>`;
            }

            outputHtml += '</ul>';

            // Affiche le résultat
            document.getElementById('result').style.display = 'block';
            document.getElementById('output').innerHTML = outputHtml;
        }
        function displayResults(data) {
            // Crée un conteneur vide pour les résultats
            let outputHtml = '<ul>';

            // Définition des unités pour chaque type de résultat
            const units = {
                eau: 'L chaque jour',
                energie: 'kcal chaque jour',
                proteines: 'gr chaque jour',
                glucides: 'gr chaque jour',
                sel: 'gr chaque jour'
            };

            // Parcourt chaque clé-valeur des résultats
            for (const [key, value] of Object.entries(data)) {
                const roundedValue = typeof value === 'number' ? parseFloat(value).toFixed(2) : value; // Arrondit à deux décimales
                const unit = units[key] ? ` ${units[key]}` : ''; // Ajoute l'unité si elle existe
                outputHtml += `<li><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${roundedValue}${unit}</li>`;
            }

            outputHtml += '</ul>';

            // Affiche le résultat
            document.getElementById('result').style.display = 'block';
            document.getElementById('output').innerHTML = outputHtml;
        }

        async function calculateNeeds() {
            const nutrient = document.getElementById('nutrientSelect').value;
            const sexe = document.getElementById('sexe').value;
            const poids = document.getElementById('poids').value;
            const taille = document.getElementById('taille').value;
            const age = document.getElementById('age').value;
            const niveauActivite = document.getElementById('niveauActivite').value;

            try {
                const response = await fetch('<?php require_once('ConfigFrontEnd.php'); echo URL_API;?>/recommandations.php', { // URL fixe pour test
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ sexe, poids, taille, age, niveauActivite, nutrient })
                });

                if (!response.ok) {
                    const errorText = await response.text(); // Pour voir la réponse si elle n'est pas JSON
                    throw new Error(`Erreur HTTP ! statut : ${response.status} - ${errorText}`);
                }

                const data = await response.json();
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Afficher le résultat
                displayResults(data);
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue, veuillez réessayer.');
            }
        }
        $(document).ready(function() {
        $('#content').css({
            opacity: 1,
            transform: 'translateY(0)'
        });
        });
    </script>
</section>
