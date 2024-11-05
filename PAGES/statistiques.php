<div id="content" style="text-align: center; display: flex; flex-direction: column; align-items: center;">
    <h2>Vos statistiques nutritionnelles</h2>
    <p id="errorParagraph" style="color: red; display: none;"></p> <!-- Message d'erreur, caché par défaut -->
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <input type="date" id="dateInput" style="margin-right: 10px;"> <!-- Champ pour sélectionner la date -->
        <button id="validateDateButton">Valider la date</button> <!-- Bouton pour valider la date -->
    </div>
    <canvas id="nutrientChart" style="max-width: 80vw; max-height: 50vh;"></canvas> <!-- Graphique pour afficher les nutriments -->
</div>

<style>
    #nutrientChart {
        width: 100%; /* 100% de la largeur disponible */
        height: auto; /* Hauteur automatique pour garder le ratio */
    }

    /* Styles pour les messages d'erreur */
    #errorParagraph {
        font-weight: bold;
        font-size: 14px;
        margin-top: 10px;
        padding: 5px;
        border: 1px solid red;
        border-radius: 4px;
        background-color: #ffe6e6; /* Fond léger pour le message d'erreur */
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let nutrientChart; // Variable pour stocker l'instance du graphique

    $(document).ready(function() {
        $('#content').css({
            opacity: 1, // Rendre le contenu visible
            transform: 'translateY(0)' // Remettre le contenu en position
        });

        // Initialiser avec la date actuelle
        const currentDate = new Date().toISOString().split('T')[0]; // format YYYY-MM-DD
        $('#dateInput').val(currentDate); // Met à jour l'input avec la date actuelle
        fetchDataAndDisplayChart(currentDate); // Afficher le graphique pour la date actuelle

        // Écouteur d'événement pour le bouton de validation
        $('#validateDateButton').on('click', function() {
            const selectedDate = $('#dateInput').val(); // Récupère la date sélectionnée
            if (!selectedDate) {
                displayErrorMessage('Veuillez sélectionner une date.'); // Message d'erreur si aucune date
                return;
            }
            fetchDataAndDisplayChart(selectedDate); // Récupérer et afficher les données pour la date sélectionnée
        });
    });

    async function fetchDataAndDisplayChart(date) {
        const loginValue = getCookie("login"); // Obtenir le login depuis le cookie
        if (!date) {
            displayErrorMessage('Veuillez sélectionner une date.'); // Vérification de la date
            return;
        }

        try {
            const apiUrl = `<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/nutrients.php?login=${loginValue}&date=${date}`;
            const response = await fetch(apiUrl); // Requête API pour obtenir les données
            const data = await response.json(); // Conversion de la réponse en JSON

            console.log("Raw Data:", data); // Log des données brutes

            if (response.ok) {
                // Vérifier le format de la réponse
                if (Array.isArray(data)) {
                    if (data.length === 0) {
                        displayEmptyChart(); // Afficher un graphique vide si aucune donnée
                        return;
                    } else {
                        console.error('Format de réponse inattendu: Array avec des données:', data);
                        displayErrorMessage('Données inattendues reçues. Veuillez vérifier la source des données.');
                        return;
                    }
                } else if (typeof data !== 'object') {
                    console.error('Format de réponse inattendu:', data);
                    throw new Error('La réponse n\'est pas au format attendu.');
                }

                // Vérification si les données sont vides
                if (Object.keys(data).length === 0) {
                    displayEmptyChart(); // Afficher un graphique vide si pas de données
                    return;
                }

                // Préparer les données pour le graphique
                const labels = Object.keys(data).map(nutrient => nutrient.replace(/\(g\/100g\)/, '')); // Noms des nutriments sans "(g/100g)"
                const values = Object.values(data); // Valeurs des nutriments

                // Définir les couleurs selon les valeurs
                const colors = values.map(value => {
                    if (value < 80) return 'yellow'; // Moins de 80%
                    else if (value >= 80 && value <= 120) return 'lightgreen'; // Entre 80% et 120%
                    else return 'red'; // Plus de 120%
                });

                // Détruire le graphique existant s'il existe
                if (nutrientChart) {
                    nutrientChart.destroy();
                }

                // Déterminer le type de graphique selon la taille de l'écran
                const isMobile = window.innerWidth <= 768; // 768px est une largeur courante pour les mobiles
                const chartType = isMobile ? 'horizontalBar' : 'bar'; // Type de graphique

                // Créer le graphique
                const ctx = document.getElementById('nutrientChart').getContext('2d');
                nutrientChart = new Chart(ctx, {
                    type: chartType, // Type de graphique (horizontal ou vertical)
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pourcentage de nutriments consommés',
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors.map(color => color), // Couleurs des bordures
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '% des AR' // Titre de l'axe X
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Nutriments' // Titre de l'axe Y
                                }
                            }
                        }
                    }
                });
            } else {
                displayErrorMessage(`Erreur: ${data.error}`); // Afficher l'erreur si la réponse n'est pas OK
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des données:', error); // Log d'erreur
            displayErrorMessage('Une erreur est survenue. Veuillez réessayer plus tard.'); // Afficher un message d'erreur
        }
    }

    // Fonction pour afficher un message d'erreur
    function displayErrorMessage(message) {
        $('#errorParagraph').text(message).show(); // Afficher le message d'erreur
        setTimeout(() => {
            $('#errorParagraph').fadeOut(); // Masquer le message après 3 secondes
        }, 3000);
    }

    // Fonction pour afficher un graphique vide
    function displayEmptyChart() {
        const ctx = document.getElementById('nutrientChart').getContext('2d');
        if (nutrientChart) {
            nutrientChart.destroy(); // Détruire le graphique existant
        }
        nutrientChart = new Chart(ctx, {
            type: 'horizontalBar', // Type de graphique par défaut
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
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '% des AR' // Titre de l'axe X
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nutriments' // Titre de l'axe Y
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
        if (parts.length === 2) return parts.pop().split(';').shift(); // Retourne la valeur du cookie
    }
</script>
