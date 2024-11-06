<section class="contact-page">
    <div id="content">
        <h1 style="color: blue">Nous Contacter</h1>
        <p>Vous avez rencontré un problème ? Envoyez-nous un message, et nous vous répondrons dans les plus brefs délais.</p>

        <form id="contactForm">
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Sujet du message :</label>
            <select id="subject" name="subject" required>
                <option value="Bug technique">Bug technique</option>
                <option value="Problème de compte">Problème de compte</option>
                <option value="Suggestion">Suggestion</option>
                <option value="Modifier le Mot de Passe">Changer le mot de Passe</option>
                <option value="Autre">Autre</option>
            </select>

            <label for="message">Description du problème :</label>
            <textarea id="message" name="message" required></textarea>
            <br>
            <button id="submit" type="submit">Envoyer</button>
        </form>

        <p id="confirmationMessage" style="display:none;">Merci ! Votre message a été envoyé. Nous reviendrons vers vous sous 24 à 48 heures.</p>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#contactForm').on('submit', function (e) {
                e.preventDefault();

                // Utiliser FormData pour gérer les données de formulaire avec un fichier joint
                const formData = new FormData(this); // Transmet tous les champs du formulaire, y compris les fichiers

                $.ajax({
                    url: '<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/bugReport.php',
                    method: 'POST',
                    processData: false, // Nécessaire pour envoyer des fichiers
                    contentType: false, // Nécessaire pour envoyer des fichiers
                    data: formData,
                    success: function (response) {
                        $('#confirmationMessage').show(); // Show confirmation message on success
                        $('#contactForm')[0].reset(); // Clear form fields
                        $('#removeFile').hide(); // Cache le bouton "Retirer le fichier" après soumission
                        $('#fileName').text('Aucun fichier sélectionné'); // Réinitialise le nom du fichier
                    },
                    error: function (xhr) {
                        console.error('Erreur lors de l’envoi du message :', xhr.responseText);
                        alert("Erreur lors de l'envoi du message. Détails : " + xhr.responseText);
                    },
                });
            });

            $('#content').css({
                opacity: 1,
                transform: 'translateY(0)'
            });
        });
    </script>
</section>
