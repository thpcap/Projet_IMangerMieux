
<section class="contact-page">
    <div id="content">
        <table id="tableContact">
            <tbody>
                <tr>
                    <th><h1>Thomas CAPRON</h1></th>
                    <td><a href="mailto:thomas.capron@example.com" type="email">thomas.capron@example.com</a></td>
                    
                </tr>
                <tr>
                    <th><h1>Yassir BENJANE</h1></th>
                    <td><a href="mailto:yabenjane@gmail.com" type="email">yabenjane@gmail.com</a></td>
                </tr>
            </tbody>
        </table>

        <div id="contactContainer">
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

                <button type="submit">Envoyer</button>
            </form>

            <p id="confirmationMessage" style="display:none;">Merci ! Votre message a été envoyé. Nous reviendrons vers vous sous 24 à 48 heures.</p>
        </div>
    </div>
</section>

