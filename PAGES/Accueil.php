<div id="appContainer">
    <h1>Liste des Aliments Consommés Depuis une Semaine</h1>

    <!-- Formulaire pour créer ou modifier un repas dans un tableau -->
    <div id="formContainer" style="display:none;">
        <h2 id="formTitle">Créer un nouveau repas</h2>
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
                <th><button id="toggleFormButton">+</button></th> <!-- Bouton dans l'en-tête -->
                <th style="display: none;">ID Repas</th> <!-- Cacher la colonne ID Repas -->
                <th>Aliment</th> <!-- Déplacer le nom de l'aliment ici -->
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
                        <tr>
                            <td></td> <!-- Cellule vide pour aligner avec le bouton -->
                            <td style="display: none;">${meal.ID_REPAS}</td> <!-- Cellule ID Repas cachée -->
                            <td>${meal.LABEL_ALIMENT}</td> <!-- Afficher le nom de l'aliment ici -->
                            <td>${meal.QUANTITE}</td>
                            <td>${new Date(meal.DATE).toLocaleDateString('fr-FR')}</td>
                            <td>
                                <button class="edit-button" data-id="${meal.ID_REPAS}" data-quantite="${meal.QUANTITE}" data-date="${new Date(meal.DATE).toISOString().split('T')[0]}" data-id-aliment="${meal.ID_ALIMENT}">Modifier</button>
                                <button class="delete-button" data-id="${meal.ID_REPAS}">Supprimer</button>
                            </td>
                        </tr>
                    `);
                });
                $('#repasTable').DataTable();

                // Événements de suppression
                $('.delete-button').on('click', function() {
                    const mealId = $(this).data('id');
                    deleteRepas(mealId);
                });

                // Événements de modification
                $('.edit-button').on('click', function() {
                    currentMealId = $(this).data('id');
                    $('#quantite').val($(this).data('quantite'));
                    $('#date').val($(this).data('date'));
                    $('#id_aliment').val($(this).data('id-aliment'));
                    $('#setNowDate').prop('checked', false); // Décocher la case à cocher
                    $('#formTitle').text('Modifier le repas'); // Changer le titre du formulaire
                    $('#submitButton').text('Mettre à jour'); // Changer le texte du bouton

                    // Affiche le formulaire
                    $('#formContainer').show();

                    // Change le texte du bouton + en - si le formulaire n'était pas déjà affiché
                    if (!$('#formContainer').is(':visible')) {
                        $('#toggleFormButton').text('-');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la récupération des repas:", error);
                alert("Erreur lors de la récupération des repas.");
            }
        });
    }

    function deleteRepas(id_repas) {
        const login = getCookie('login');
        $.ajax({
            url: `${apiUrl}`,
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

    $('#createMealForm').on('submit', function(event) {
        event.preventDefault();

        const date = $('#date').val();
        const isCheckboxChecked = $('#setNowDate').is(':checked');

        if (!date && !isCheckboxChecked) {
            alert("Veuillez remplir la date ou cocher 'Utiliser la date actuelle'.");
            return;
        }

        const quantite = $('#quantite').val();
        const id_aliment = $('#id_aliment').val();
        const login = getCookie('login');
        const finalDate = isCheckboxChecked ? new Date().toISOString().split('T')[0] : date;

        if (currentMealId) {
            // Si nous modifions un repas
            $.ajax({
                url: `${apiUrl}`,
                method: 'PUT', // Utiliser PUT pour mettre à jour
                contentType: 'application/json',
                data: JSON.stringify({ login: login, id_repas: currentMealId, quantite: quantite, date: finalDate, id_aliment: id_aliment }),
                success: function(response) {
                    alert("Repas mis à jour avec succès.");
                    fetchRepas();
                    resetForm();
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de la mise à jour du repas:", xhr.responseText);
                    alert("Erreur lors de la mise à jour du repas.");
                }
            });
        } else {
            // Sinon, nous ajoutons un nouveau repas
            $.ajax({
                url: apiUrl,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ login: login, quantite: quantite, date: finalDate, id_aliment: id_aliment }),
                success: function(response) {
                    alert("Repas ajouté avec succès.");
                    fetchRepas();
                    resetForm();
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de l'ajout du repas:", xhr.responseText);
                    alert("Erreur lors de l'ajout du repas.");
                }
            });
        }
    });

    // Fonction pour réinitialiser le formulaire
    function resetForm() {
        $('#createMealForm')[0].reset();
        $('#setNowDate').prop('checked', false);
        $('#formTitle').text('Créer un nouveau repas');
        $('#submitButton').text('Ajouter Repas');
        currentMealId = null; // Réinitialiser l'ID du repas
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Afficher ou cacher le formulaire lors du clic sur le bouton +
    $('#toggleFormButton').on('click', function() {
        $('#formContainer').toggle(); // Alterne la visibilité du formulaire
        // Change le texte du bouton selon la visibilité du formulaire
        if ($('#formContainer').is(':visible')) {
            $('#toggleFormButton').text('-');
        } else {
            $('#toggleFormButton').text('+');
        }
    });


    fetchRepas();
});
</script>
