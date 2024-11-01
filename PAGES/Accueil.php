<div id="appContainer">
    <h1>Liste des Aliments Consommés Depuis une Semaine</h1>

    <!-- Formulaire uniquement pour l'ajout d'un repas -->
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
$(document).ready(function() {
    const apiUrl = '<?php require_once('ConfigFrontEnd.php'); echo URL_API;?>/repas.php';
    let currentMealId = null; // ID du repas actuellement sélectionné pour modification

    function fetchRepas() {
        const login = getCookie('login');
        $.ajax({
            url: `${apiUrl}?login=${login}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const tableBody = $('#repasTable tbody');
                tableBody.empty(); // Vide le corps de la table avant de remplir

                data.forEach(meal => {
                    tableBody.append(`
                        <tr data-id="${meal.ID_REPAS}">
                            <td></td> <!-- Cellule vide pour aligner avec le bouton -->
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

                // Attacher les événements de modification et de suppression
                attachTableEvents();
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la récupération des repas:", error);
                alert("Erreur lors de la récupération des repas.");
            }
        });
    }

    // Fonction pour attacher les événements de modification et de suppression
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

                // Appeler la fonction pour mettre à jour
                updateRepas(id_repas, quantite, date);

                // Désactiver l'édition
                row.find('[data-field="quantite"]').attr('contenteditable', 'false').removeClass('editing');
                row.find('[data-field="date"]').attr('contenteditable', 'false').removeClass('editing');
                $(this).text('Modifier'); // Revenir au texte "Modifier"
            }
        });

        $('.delete-button').on('click', function() {
            const id_repas = $(this).closest('tr').data('id');
            deleteRepas(id_repas);
        });
    }

    // Fonction pour mettre à jour un repas spécifique
    function updateRepas(id_repas, quantite, date) {
        const login = getCookie('login');
        const updateData = {
            login: login,
            id_repas: id_repas,
            quantite: quantite,
            date: new Date(date.split('/').reverse().join('-')).toISOString().split('T')[0] // Formatage de la date
        };

        $.ajax({
            url: apiUrl,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(updateData),
            success: function(response) {
                alert("Repas mis à jour avec succès.");
                fetchRepas(); // Recharger les données pour afficher la mise à jour
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la mise à jour du repas:", xhr.responseText);
                alert("Erreur lors de la mise à jour du repas.");
            }
        });
    }

    // Fonction pour supprimer un repas
    function deleteRepas(id_repas) {
        const login = getCookie('login');
        $.ajax({
            url: apiUrl,
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ login: login, id_repas: id_repas }),
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

    // Gestion du formulaire de création
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

    // Charger initialement les repas
    fetchRepas();
});
</script>
