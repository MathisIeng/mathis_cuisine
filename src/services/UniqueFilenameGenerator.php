<?php

namespace App\services;

class UniqueFilenameGenerator
{

    public function generateUniqueFilename($filename, $extension) {

        $currentTimestamp = time();
        $nameHashed = hash('sha256', $filename);

        $imageNewName = uniqid() . '-' . $nameHashed . '-' . $currentTimestamp . '.' . $extension;

        return $imageNewName;
    }
}

// Un test unitaire teste de manière automatique une fonctionnalité (une classe ou plusieurs classes)
// Un test fonctionnel (e2e) teste de manière automatique une fonctionnalité : en imitant l'utilisateur, donc charger une page
// Vérifier quand je clique sur le bouton de suppression que l'élément est bien supprimé en BDD