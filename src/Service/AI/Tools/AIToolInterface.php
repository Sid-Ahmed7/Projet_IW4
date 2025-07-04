<?php

namespace App\Service\AI\Tools;

interface AIToolInterface
{
    /**
     * Exécute l'outil et retourne les données
     * @return array Les données retournées par l'outil
     */
    public function execute(): array;

    /**
     * Retourne le nom de l'outil
     * @return string
     */
    public function getName(): string;
} 