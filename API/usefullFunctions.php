<?php
    function escape_special_characters($string) {
        // Liste des caractères spéciaux à échapper
        $special_characters = "\0\n\r\t\\'\"\x1a\|\&\`\#"; // Caractères spéciaux communs
        // On utilise addcslashes pour ajouter des \ devant les caractères spéciaux
        return addcslashes($string, $special_characters);
    }
    function unescape_special_characters_in_object($object) {
        foreach ($object as $key => $value) {
            if (is_string($value)) {
                // Appliquer stripslashes uniquement sur les propriétés qui sont des chaînes de caractères
                $object->$key = stripslashes($value);
            }
        }
        return $object;
    }
    
    function setHeaders() {
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
        header("Access-Control-Allow-Origin: *");
        header('Content-type: application/json; charset=utf-8');
    }