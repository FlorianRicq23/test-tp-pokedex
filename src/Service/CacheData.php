<?php

namespace App\Service;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheData
{
    public function getCachePokemonGenerationList($liste_temp): array
    {
        $cache = new FilesystemAdapter();
        $pokemonGenerationList = $cache->getItem('pokemon_generation');
        if (!$pokemonGenerationList->isHit()) {
            $pokemonGenerationList->set($liste_temp);
            $cache->save($pokemonGenerationList);
        }
        return $pokemonGenerationList->get();  
    }
}