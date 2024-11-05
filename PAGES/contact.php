
<section class="contact-page">
    <div id="content">
            <h1>Nous Contacter</h1>
            <p>Vous avez rencontré un problème ? Envoyez-nous un message, et nous vous répondrons dans les plus brefs délais.</p>

            <form id="contactForm" >
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="subject">Sujet du message :</label>
                <select id="subject" name="subject" required>
                    <option value="Bug technique">Bug technique</option>
                    <option value="Problème de compte">Problème de compte</option>
                    <option value="Suggestion">Suggestion</option>
                    <option value="Autre">Autre</option>
                </select>

                <label for="message">Description du problème :</label>
                <textarea id="message" name="message" ></textarea>

                <label for="attachment">Pièces jointes (facultatif) :</label>
                <input type="file" id="attachment" name="attachment">

                <button id="submit" type="submit">Envoyer</button>
            </form>

            <p id="confirmationMessage" style="display:none;">Merci ! Votre message a été envoyé. Nous reviendrons vers vous sous 24 à 48 heures.</p>
    </div>
    <script>
        
        $(document).ready(function () {
            $('#contactForm').on('submit', function (e) {
                e.preventDefault();

                // Gather form data
                const formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    subject: $('#subject').val(),
                    message: $('#message').val(),
                };

                // AJAX request to send data to the PHP file
                $.ajax({
                    url: '<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/bugReport.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function (response) {
                        $('#confirmationMessage').show(); // Show confirmation message on success
                        $('#contactForm')[0].reset(); // Clear form fields
                    },
                    error: function (xhr, status, error) {
                        console.error('Erreur lors de l’envoi du message :', error);
                        alert("Erreur lors de l'envoi du message. veuillez nous contacter à l'adresse imangermieux@gmail.com ");
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

