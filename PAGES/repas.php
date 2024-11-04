<div id="appContainer" style="margin: auto; margin-top:20px">
    <h1>Liste des Aliments Consommés</h1>

    <!-- MODIFIÉ : Ajout de la liste déroulante pour sélectionner l'intervalle -->
    <label for="interval-selection">Afficher par :</label>
    <select id="interval-selection" onchange="toggleDateInput()">
        <option value="day">Jour</option>
        <option value="week" selected>Semaine</option>
        <option value="month">Mois</option>
    </select>

    <!-- MODIFIÉ : Champ de sélection de la date pour "Jour" -->
    <input type="date" id="date-input" style="display: none;" />

    <button onclick="fetchRepas()">Afficher les repas</button>

    <!-- Formulaire pour l'ajout d'un repas -->
    <div id="formContainer" style="display:none;">
        <h2>Créer un nouveau repas</h2>
        <form id="createMealForm">
            <table>
                <tr>
                    <td><label for="quantite">Quantité :</label></td>
                    <td><input type="number" id="quantite" name="quantite" required></td>
                </tr>
                <tr>
                    <td><label for="date">Date :</label></td>
                    <td>
                        <input type="date" id="date" name="date">
                        <input type="checkbox" id="setNowDate" name="setNowDate">
                        <label for="setNowDate">Utiliser la date actuelle</label>
                    </td>
                </tr>
                <tr>
                    <td><label for="id_aliment">ID Aliment :</label></td>
                    <td><input type="number" id="id_aliment" name="id_aliment" required></td>
                </tr>
                <tr>
                    <td colspan="2"><button type="submit" id="submitButton">Ajouter Repas</button></td>
                </tr>
            </table>
        </form>
    </div>

    <table id="repasTable" class="display">
        <thead>
            <tr>
                <th><button id="toggleFormButton">+</button></th>
                <th style="display: none;">ID Repas</th>
                <th>Aliment</th>
                <th>Quantité</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Les données seront peuplées dynamiquement via JavaScript -->
        </tbody>
    </table>
</div>

<script>
const apiUrl = '<?php require_once('ConfigFrontEnd.php'); echo URL_API;?>/repas.php';

// Afficher ou masquer le champ de date en fonction de l'intervalle sélectionné
function toggleDateInput() {
    const intervalSelection = document.getElementById("interval-selection").value;
    const dateInput = document.getElementById("date-input");
    dateInput.style.display = intervalSelection === "day" ? "block" : "none";
}

// Fonction pour récupérer les repas en fonction de l'intervalle sélectionné et de la date
function fetchRepas() {
    const login = getCookie('login');
    const interval = document.getElementById("interval-selection").value;
    const date = interval === "day" ? document.getElementById("date-input").value : null;

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
            // Réinitialiser DataTable pour éviter les doublons
            if ($.fn.DataTable.isDataTable('#repasTable')) {
                $('#repasTable').DataTable().clear().destroy();
            }            
            tableBody.empty();
            data.forEach(meal => {
                tableBody.append(`
                    <tr data-id="${meal.ID_REPAS}">
                        <td></td>
                        <td style="display: none;">${meal.ID_REPAS}</td>
                        <td>${meal.LABEL_ALIMENT}</td>
                        <td contenteditable="false" class="editable-cell" data-field="quantite">${meal.QUANTITE}</td>
                        <td contenteditable="false" class="editable-cell" data-field="date">${new Date(meal.DATE).toLocaleDateString('fr-FR')}</td>
                        <td>
                            <button class="edit-button">Modifier</button>
                            <button class="delete-button">Supprimer</button>
                        </td>
                    </tr>
                `);
            });
            $('#repasTable').DataTable();
            attachTableEvents();
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors de la récupération des repas:", error);
            alert("Erreur lors de la récupération des repas.");
        }
    });
}

// Attacher les événements de modification et de suppression des repas
function attachTableEvents() {
    $('.edit-button').on('click', function() {
        const row = $(this).closest('tr');
        const isEditing = $(this).text() === 'Enregistrer';

        if (!isEditing) {
            // Activer l'édition
            row.find('[data-field="quantite"]').attr('contenteditable', 'true').addClass('editing');
            row.find('[data-field="date"]').attr('contenteditable', 'true').addClass('editing');
            $(this).text('Enregistrer'); // Changer le texte du bouton en "Enregistrer"
        } else {
            // Sauvegarder les modifications
            const id_repas = row.data('id');
            const quantite = row.find('[data-field="quantite"]').text().trim();
            const date = row.find('[data-field="date"]').text().trim();

            updateRepas(id_repas, quantite, date);

            row.find('[data-field="quantite"]').attr('contenteditable', 'false').removeClass('editing');
            row.find('[data-field="date"]').attr('contenteditable', 'false').removeClass('editing');
            $(this).text('Modifier');
        }
    });

    $('.delete-button').on('click', function() {
        const id_repas = $(this).closest('tr').data('id');
        deleteRepas(id_repas);
    });
}

// Mettre à jour un repas spécifique
function updateRepas(id_repas, quantite, date) {
    const login = getCookie('login');
    const updateData = {
        login: login,
        id_repas: id_repas,
        quantite: quantite,
        date: new Date(date.split('/').reverse().join('-')).toISOString().split('T')[0]
    };

    $.ajax({
        url: apiUrl,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(updateData),
        success: function(response) {
            alert("Repas mis à jour avec succès.");
            fetchRepas();
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors de la mise à jour du repas:", xhr.responseText);
            alert("Erreur lors de la mise à jour du repas.");
        }
    });
}

// Supprimer un repas
function deleteRepas(id_REPAS) {
    const login = getCookie('login');
    $.ajax({
        url: apiUrl,
        method: 'DELETE',
        contentType: 'application/json',
        data: JSON.stringify({ login: login, id_REPAS: id_REPAS }),
        success: function(response) {
            alert("Repas supprimé avec succès.");
            fetchRepas();
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors de la suppression du repas:", xhr.responseText);
            alert("Erreur lors de la suppression du repas.");
        }
    });
}

// Formulaire de création de repas
$('#createMealForm').on('submit', function(event) {
    event.preventDefault();

    const quantite = $('#quantite').val();
    const date = $('#setNowDate').is(':checked') ? new Date().toISOString().split('T')[0] : $('#date').val();
    const id_aliment = $('#id_aliment').val();

    $.ajax({
        url: apiUrl,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ login: getCookie('login'), quantite: quantite, date: date, id_aliment: id_aliment }),
        success: function(response) {
            alert("Repas ajouté avec succès.");
            fetchRepas();
            $('#createMealForm')[0].reset();
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors de l'ajout du repas:", xhr.responseText);
            alert("Erreur lors de l'ajout du repas.");
        }
    });
});

// Fonction pour obtenir le cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Afficher ou cacher le formulaire lors du clic sur le bouton +
$('#toggleFormButton').on('click', function() {
    $('#formContainer').toggle();
    $(this).text($('#formContainer').is(':visible') ? '-' : '+');
});

$(document).ready(function() {
    fetchRepas(); // Charger initialement les repas
});
</script>

