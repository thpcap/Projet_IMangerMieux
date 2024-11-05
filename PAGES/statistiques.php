<div id="content" style="text-align: center; display: flex; flex-direction: column; align-items: center;">
    <h2>Vos statistiques nutritionnelles</h2>
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <input type="date" id="dateInput" style="margin-right: 10px;"> <!-- Ajout d'une marge droite -->
        <button id="validateDateButton">Valider la date</button>
    </div>
    <canvas id="nutrientChart" width="400" height="200" style="max-width: 80vw; max-height: 50vh;"></canvas>
    <p id="errorParagraph" style="color: red; display: none;"></p> <!-- Paragraphe pour les messages d'erreur -->
</div>


<style>
    #nutrientChart {
        width: 80%; /* Limiter à 80% de la largeur de l'écran */
        height: auto; /* Hauteur automatique pour garder le ratio */
    }
</style>


<script>
    let nutrientChart; // Déclaration de la variable pour stocker le graphique

    $(document).ready(function() {
        $('#content').css({
            opacity: 1,
            transform: 'translateY(0)'
        });

        // Charger les données de la date actuelle au démarrage
        const currentDate = new Date().toISOString().split('T')[0]; // format YYYY-MM-DD
        $('#dateInput').val(currentDate); // Met à jour l'input avec la date actuelle
        fetchDataAndDisplayChart(currentDate); // Appelle la fonction pour afficher le graphique

        // Event listener pour le bouton de validation
        $('#validateDateButton').on('click', function() {
            const selectedDate = $('#dateInput').val(); // Récupère la date sélectionnée
            if (!selectedDate) {
                displayErrorMessage('Veuillez sélectionner une date.'); // Affiche un message d'erreur si aucune date n'est sélectionnée
                return;
            }
            fetchDataAndDisplayChart(selectedDate); // Appelle la fonction pour récupérer les données
        });
    });

    async function fetchDataAndDisplayChart(date) {
        const loginValue = getCookie("login"); // Obtenez le login depuis le cookie
        if (!date) {
            displayErrorMessage('Veuillez sélectionner une date.');
            return;
        }

        try {
            const apiUrl = `<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/nutrients.php?login=${loginValue}&date=${date}`;
            const response = await fetch(apiUrl);
            const data = await response.json();

            // Log pour voir la réponse brute
            console.log("Raw Data:", data); // Log de la réponse brute

            if (response.ok) {
                // Vérifiez si la réponse est un tableau
                if (Array.isArray(data)) {
                    if (data.length === 0) {
                        // Afficher un graphique vide
                        displayEmptyChart();
                        return; // Sortir de la fonction
                    } else {
                        console.error('Format de réponse inattendu: Array avec des données:', data);
                        displayErrorMessage('Données inattendues reçues. Veuillez vérifier la source des données.');
                        return; // Sortir de la fonction si ce n'est pas un format valide
                    }
                } else if (typeof data !== 'object') {
                    console.error('Format de réponse inattendu:', data);
                    throw new Error('La réponse n\'est pas au format attendu.');
                }

                // Vérifiez si les données sont vides
                if (Object.keys(data).length === 0) {
                    // Afficher un graphique vide
                    displayEmptyChart();
                    return; // Sortir de la fonction
                }

                // Préparer les données pour Chart.js
                const labels = Object.keys(data).map(nutrient => nutrient.replace(/ \(g\/100g\)/, '')); // Noms des nutriments sans "(g/100g)"
                const values = Object.values(data); // Valeurs des nutriments

                // Définir les couleurs des barres selon les pourcentages
                const colors = values.map(value => {
                    if (value < 80) return 'yellow'; // Moins de 80%
                    else if (value >= 80 && value <= 120) return 'lightgreen'; // Entre 80% et 120%
                    else return 'red'; // Plus de 120%
                });

                // Si le graphique existe déjà, détruire l'instance précédente
                if (nutrientChart) {
                    nutrientChart.destroy(); // Détruire le graphique existant
                }

                // Afficher le graphique
                const ctx = document.getElementById('nutrientChart').getContext('2d');
                nutrientChart = new Chart(ctx, {
                    type: 'bar', // Vous pouvez changer en 'pie' pour un graphique circulaire
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pourcentage de nutriments consommés',
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors.map(color => color), // Couleurs des bordures identiques aux barres
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '% des AR'
                                }
                            }
                        }
                    }
                });
            } else {
                displayErrorMessage(`Erreur: ${data.error}`);
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des données:', error);
            displayErrorMessage('Une erreur est survenue. Veuillez réessayer plus tard.');
        }
    }

    // Fonction pour afficher un message d'erreur
    function displayErrorMessage(message) {
        $('#errorParagraph').text(message).show(); // Affiche le message d'erreur
        setTimeout(() => {
            $('#errorParagraph').fadeOut(); // Cache le message après 3 secondes
        }, 3000);
    }

    // Fonction pour afficher un graphique vide
    function displayEmptyChart() {
        const ctx = document.getElementById('nutrientChart').getContext('2d');
        if (nutrientChart) {
            nutrientChart.destroy(); // Détruire le graphique existant
        }
        nutrientChart = new Chart(ctx, {
            type: 'bar', // Type de graphique
            data: {
                labels: [], // Pas de labels
                datasets: [{
                    label: 'Pourcentage de nutriments consommés',
                    data: [], // Pas de données
                    backgroundColor: [], // Pas de couleurs
                    borderColor: [], // Pas de bordures
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '% des AR'
                        }
                    }
                }
            }
        });
    }

    // Fonction pour obtenir la valeur d'un cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }


</script>
