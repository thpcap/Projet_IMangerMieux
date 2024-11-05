<div id="content" style="text-align: center; height:100%">
    <h1 style="color: blue;">Bienvenue</h1>
    <img src="../logo/upper_logo.png" alt="" style="box-shadow:0 0 10px rgba(0, 0, 0, 0.2); border-radius:50%; height:50%; max-height:200px;">
    <h2>IMangerMieux</h2>
    <p style="color: blue;"><strong><u>Votre allié pour une nutrition saine et équilibrée.</u></strong></p>
    <h3>Site de gestion de la nutrition</h3>
    <p>Pour commencer, veuillez ajouter des repas.</p>
    <br>
    <p style="margin:auto; bottom: 10px; text-align:center; font-size:small;">Ce projet a été développé lors d'un cours de développement Web à <a href="https://imt-nord-europe.fr/">IMT Nord Europe</a>.</p>
</div>

<script>
    $(document).ready(function() {
        $('#content').css({
            opacity: 1,
            transform: 'translateY(0)'
        });
    });
</script>

<style>
    #content {
        background-color: #f0f8ff; /* Couleur de fond douce pour la section */
        padding: 20px; /* Espacement intérieur */
        border-radius: 15px; /* Coins arrondis */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Ombre pour donner du relief */
        max-width: 600px; /* Limite la largeur maximale */
        margin: 20px auto; /* Centre le contenu et ajoute un espace en haut et en bas */
        transition: transform 0.5s ease, opacity 0.5s ease; /* Transition douce pour l'apparition */
        opacity: 0; /* Initialement caché pour l'animation */
        transform: translateY(-20px); /* Déplacement vers le bas pour l'animation */
    }

    h1 {
        font-size: 2.5em; /* Augmente la taille du titre principal */
        margin-bottom: 10px; /* Espace en bas du titre */
    }

    h2 {
        color: #FF4500; /* Couleur orangée pour le sous-titre */
        margin: 10px 0; /* Espace en haut et en bas du sous-titre */
    }

    p {
        font-size: 1.1em; /* Augmente légèrement la taille du texte */
        line-height: 1.6; /* Espace entre les lignes pour une meilleure lisibilité */
        margin: 10px 0; /* Espacement vertical entre les paragraphes */
    }

    a {
        color: #007BFF; /* Couleur bleue pour les liens */
        text-decoration: none; /* Enlève le soulignement par défaut */
        transition: color 0.3s; /* Transition douce pour la couleur */
    }

    a:hover {
        color: #0056b3; /* Couleur plus sombre au survol du lien */
        text-decoration: underline; /* Souligne le texte lors du survol */
    }

    img {
        transition: transform 0.3s; /* Transition douce pour l'image */
    }

</style>