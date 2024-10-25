

    <!-- Table pour afficher les utilisateurs -->
<table id="repasTable" class="display">
        <thead>
            <tr>
                <th colspan="4"><h1>Gestion des Repas</h1></th>
            </tr>
            <tr>
                <th>ID_REPAS</th>
                <th>Quantite</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Les données seront peuplées dynamiquement via AJAX -->
        </tbody>
</table>

<h3 id="formTitle">Ajouter un utilisateur</h3>
<form id="addRepasForm">
        <input type="hidden" id="repasId"> <!-- Champ caché pour stocker l'ID de l'utilisateur lors de la modification -->
        <label>Quantite: </label><input type="text" id="quantite" ><br>
        <label>Date: </label><input type="date" id="date" ><br>
        <button type="submit">Ajouter</button>
</form>

<script>
        $(document).ready(function() {
            let isEditingRow = null; // Stocker la ligne en cours d'édition
            let table = $('#repasTable').DataTable({
                ajax: {
                    url: '<?php require_once('ConfigFrontEnd.php'); echo URL_API;?>/accueil_aliment.php', // Met à jour avec le chemin correct de ton API
                    dataSrc: '',
                    error: function(xhr, error, thrown) {
                        console.log(xhr.responseText); // Affiche les détails de l'erreur dans la console
                        alert("Erreur lors du chargement des utilisateurs : " + xhr.status + " " + thrown);
                    }
                },
                columns: [
                    { data: 'ID_REPAS' },
                    { data: 'QUANTITE' },
                    { data: 'DATE' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <button class="editBtn">Modifier</button>
                                <button class="saveBtn" style="display:none;">Sauvegarder</button>
                                <button class="deleteBtn">Supprimer</button>
                                
                            `;
                        }
                    }
                ]
            });

            // Gérer la soumission du formulaire pour ajouter un utilisateur
            $('#addRepasForm').on('submit', function(e) {
                e.preventDefault();
                let quantite = $('#quantite').val();
                let date = $('#date').val();

                // Mode ajout : envoyer une requête POST pour ajouter un nouvel utilisateur
                $.ajax({
                    url: 'http://localhost/Projet_IMangerMieux/Projet_IMangerMieux/API/accueil_aliment.php',
                    method: 'POST',
                    data: JSON.stringify({ QUANTITE: quantite, DATE: date }),
                    contentType: 'application/json',
                    success: function(response) {
                        table.ajax.reload(); // Recharger les données de la table
                        alert('Repas ajouté avec succès');
                        resetForm();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert("Erreur lors de l'ajout du repas : " + xhr.status + " " + error);
                    }
                });
            });

            // Gérer le bouton "Modifier" directement dans le tableau
            $('#repasTable tbody').on('click', '.editBtn', function() {
                let row = $(this).closest('tr'); // Sélectionner la ligne parent
                let data = table.row(row).data();

                if (isEditingRow) {
                    alert("Une autre ligne est déjà en cours de modification. Sauvegardez ou annulez les modifications.");
                    return;
                }

                isEditingRow = row;

                // Rendre les cellules de la ligne éditables
                row.find('td:eq(1)').html(`<input type="text" value="${data.quantite}" class="edit-quantite">`);
                row.find('td:eq(2)').html(`<input type="date" value="${data.date}" class="edit-date">`);

                // Afficher le bouton "Sauvegarder" et cacher le bouton "Modifier"
                row.find('.editBtn').hide();
                row.find('.saveBtn').show();
            });

            // Gérer le bouton "Sauvegarder"
            $('#repasTable tbody').on('click', '.saveBtn', function() {
                let row = $(this).closest('tr'); // Sélectionner la ligne parent
                let repasId = row.find('td:eq(0)').text();
                let newQuantite = row.find('.edit-quantite').val();
                let newDate = row.find('.edit-date').val();

                // Envoyer la mise à jour via AJAX
                $.ajax({
                    url: `http://localhost/Projet_IMangerMieux/Projet_IMangerMieux/API/accueil_aliment.php?id=${repasId}`,
                    method: 'PUT',
                    data: JSON.stringify({ ID_REPAS: repasId, QUANTITE: newQuantite, DATE: newDate }),
                    contentType: 'application/json',
                    success: function(response) {
                        // Mettre à jour les cellules du tableau avec les nouvelles valeurs
                        row.find('td:eq(1)').text(newQuantite);
                        row.find('td:eq(2)').text(newDate);

                        // Réinitialiser les boutons
                        row.find('.editBtn').show();
                        row.find('.saveBtn').hide();

                        // Réinitialiser la ligne d'édition
                        isEditingRow = null;
                        alert('Repas modifié avec succès.');
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert("Erreur lors de la modification du Repas : " + xhr.status + " " + error);
                    }
                });
            });

            // Gérer le bouton "Supprimer"
            $('#repasTable tbody').on('click', '.deleteBtn', function() {
                let row = $(this).closest('tr');
                let data = table.row(row).data();
                if (confirm('Êtes-vous sûr de vouloir supprimer ce repas ?')) {
                    $.ajax({
                        url: `http://localhost/Projet_IMangerMieux/Projet_IMangerMieux/API/accueil_aliment.php?id=${data.ID_REPAS}`,
                        method: 'DELETE',
                        success: function() {
                            table.ajax.reload(); // Recharger les données
                            alert('Repas supprimé');
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            alert("Erreur lors de la suppression du repas: " + xhr.status + " " + error);
                        }
                    });
                }
            });

            // Fonction pour réinitialiser le formulaire
            function resetForm() {
                $('#quantite').val('');
                $('#date').val('');
            }
        });
</script>


