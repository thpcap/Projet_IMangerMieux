<div id="content" style="margin: auto; margin-top: 20px; max-width: 800px; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); background-color: #f9f9f9;">
    <h1 style="text-align: center; color: #333;">Liste des Aliments Consommés</h1>

    <!-- Sélection de l'intervalle de filtrage -->
    <label for="interval-selection" style="font-weight: bold;">Afficher par :</label>
    <select id="interval-selection" style="margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
        <option value="day" selected>Jour</option>
        <option value="week">Semaine</option>
        <option value="month">Mois</option>
    </select>

    <!-- Champ de sélection de la date (pour jour et mois) -->
    <input type="date" id="date-input" style="display: block; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc;" />

    <!-- Formulaire pour l'ajout d'un repas -->
    <div id="formContainer" style="display:none; margin-top: 20px; padding: 15px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color: #555;">Créer un nouveau repas</h2>
        <form id="createMealForm">
            <table style="width: 100%;">
                <tr>
                    <td><label for="quantite">Quantité en g: </label></td>
                    <td><input type="number" id="quantite" name="quantite" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"></td>
                </tr>
                <tr>
                    <td><label for="date">Date :</label></td>
                    <td>
                        <input type="date" id="date" name="date" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                        <input type="checkbox" id="setNowDate" name="setNowDate">
                        <label for="setNowDate">Utiliser la date actuelle</label>
                    </td>
                </tr>
                <tr>
                    <td><label for="id_aliment">Aliment :</label></td>
                    <td>
                        <select id="id_aliment" name="id_aliment" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                            <!-- Options des aliments ajoutées dynamiquement ici -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><button type="submit" id="submitButton" style="background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer;">Ajouter Repas</button></td>
                </tr>
            </table>
        </form>
    </div>
    <p id="error-message" style="display: none; color: #DC3545; text-align: center; font-weight: bold;"></p>
    <table id="repasTable" class="display" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th><button id="toggleFormButton" style="background-color: #007BFF; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">+</button></th>
                <th style="display: none;">ID REPAS</th>
                <th style="text-align: left;">Aliment</th>
                <th style="text-align: left;">Quantité en g</th>
                <th style="text-align: left;">Date</th>
                <th style="text-align: left;">Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    const apiUrl = '<?php require_once('ConfigFrontEnd.php'); echo URL_API;?>/repas.php';
    const alimentsUrl = '<?php require_once('ConfigFrontEnd.php'); echo URL_API;?>/aliments.php?aliments=true';
    let currentMealId = null;

    function displayError(message) {
        const errorMessage = $('#error-message');
        errorMessage.text(message).show(); // Afficher le message d'erreur
        setTimeout(() => {
            errorMessage.fadeOut(); // Cacher le message après 5 secondes
        }, 5000);
    }

    function fetchRepas() {
        const login = getCookie('login');
        const interval = document.getElementById("interval-selection").value;
        const date = (interval === "day" || interval === "month") ? document.getElementById("date-input").value : null;

        let url = `${apiUrl}?login=${login}&interval=${interval}`;
        if (date) {
            url += `&date=${date}`;
        }

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const tableBody = $('#repasTable tbody');
                tableBody.empty();

                data.forEach(meal => {
                    tableBody.append(`
                        <tr data-id="${meal.ID_REPAS}">
                            <td></td>
                            <td style="display: none;">${meal.ID_REPAS}</td>
                            <td>${meal.LABEL_ALIMENT}</td>
                            <td contenteditable="false" class="editable-cell" data-field="quantite">${meal.QUANTITE*100}</td>
                            <td contenteditable="false" class="editable-cell" data-field="date">${new Date(meal.DATE).toLocaleDateString('fr-FR')}</td>
                            <td>
                                <button class="edit-button" style="background-color: #FFC107; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Modifier</button>
                                <button class="delete-button" style="background-color: #DC3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Supprimer</button>
                            </td>
                        </tr>
                    `);
                });

                $('#repasTable').DataTable();
                attachTableEvents();
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la récupération des repas:", error);
                displayError("Erreur lors de la récupération des repas.");
            }
        });
    }

    // Charger la liste des aliments au chargement de la page
    function loadAliments() {
        $.ajax({
            url: alimentsUrl,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const alimentSelect = $('#id_aliment');
                alimentSelect.empty(); // Vider les anciennes options
                data.forEach(aliment => {
                    alimentSelect.append(`<option value="${aliment.ID_ALIMENT}">${aliment.LABEL_ALIMENT}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la récupération des aliments:", error);
                displayError("Erreur lors de la récupération des aliments.");
            }
        });
    }

    loadAliments(); // Appeler la fonction pour charger les aliments

    $('#createMealForm').on('submit', function(event) {
        event.preventDefault();

        const quantite = $('#quantite').val();
        const date = $('#setNowDate').is(':checked') ? new Date().toISOString().split('T')[0] : $('#date').val();
        const id_aliment = $('#id_aliment').val();

        $.ajax({
            url: apiUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ login: getCookie('login'), quantite: (quantite/100), date: date, id_aliment: id_aliment }),
            success: function(response) {
                displayError("Repas ajouté avec succès.");
                fetchRepas();
                $('#createMealForm')[0].reset();
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de l'ajout du repas:", xhr.responseText);
                displayError("Erreur lors de l'ajout du repas.");
            }
        });
    });

    function attachTableEvents() {
        $('.edit-button').on('click', function() {
            const row = $(this).closest('tr');
            const isEditing = $(this).text() === 'Enregistrer';

            if (!isEditing) {
                row.find('[data-field="quantite"]').attr('contenteditable', 'true').addClass('editing');
                row.find('[data-field="date"]').attr('contenteditable', 'true').addClass('editing');
                $(this).text('Enregistrer');

                // Ajout de la classe highlight
                row.find('[data-field="quantite"]').addClass('highlight');
                row.find('[data-field="date"]').addClass('highlight');

            } else {
                const ID_REPAS = row.data('id');
                const quantite = row.find('[data-field="quantite"]').text().trim();
                const date = row.find('[data-field="date"]').text().trim();

                updateRepas(ID_REPAS, quantite, date);

                row.find('[data-field="quantite"]').attr('contenteditable', 'false').removeClass('editing');
                row.find('[data-field="date"]').attr('contenteditable', 'false').removeClass('editing');
                $(this).text('Modifier');

                // Retirer la classe highlight après l'enregistrement
                row.find('[data-field="quantite"]').removeClass('highlight');
                row.find('[data-field="date"]').removeClass('highlight');
            }
        });

        $('.delete-button').on('click', function() {
            const ID_REPAS = $(this).closest('tr').data('id');
            deleteRepas(ID_REPAS);
        });
    }

    function updateRepas(ID_REPAS, quantite, date) {
        const login = getCookie('login');
        const updateData = {
            login: login,
            ID_REPAS: ID_REPAS,
            quantite: (quantite/100),
            date: new Date(date.split('/').reverse().join('-')).toISOString().split('T')[0]
        };

        $.ajax({
            url: apiUrl,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(updateData),
            success: function(response) {
                displayError("Repas mis à jour avec succès.");
                fetchRepas();
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la mise à jour du repas:", xhr.responseText);
                displayError("Erreur lors de la mise à jour du repas.");
            }
        });
    }

    function deleteRepas(ID_REPAS) {
        const login = getCookie('login');
        $.ajax({
            url: apiUrl,
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ login: login, ID_REPAS: ID_REPAS }),
            success: function(response) {
                displayError("Repas supprimé avec succès.");
                fetchRepas();
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la suppression du repas:", xhr.responseText);
                displayError("Erreur lors de la suppression du repas.");
            }
        });
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function toggleDateInput() {
        const interval = document.getElementById("interval-selection").value;
        const dateInput = document.getElementById("date-input");
        dateInput.style.display = (interval === "day" || interval === "month") ? "block" : "none";
    }

    function setDefaultDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("date-input").value = today;
    }

    setDefaultDate(); // Définir la date actuelle par défaut
    fetchRepas(); // Appeler l'affichage des repas dès le chargement de la page

    document.getElementById("interval-selection").addEventListener("change", function () {
        toggleDateInput();
        fetchRepas();
    });

    document.getElementById("date-input").addEventListener("change", fetchRepas);

    $('#toggleFormButton').on('click', function() {
        $('#formContainer').toggle();
        $(this).text($('#formContainer').is(':visible') ? '-' : '+');
    });

    $('#content').css({
        opacity: 1,
        transform: 'translateY(0)'
    });

});
</script>
