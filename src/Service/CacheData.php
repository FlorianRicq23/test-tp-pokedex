<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheData
{
    public function getCachePokemonGenerationList($generation, $pokeClient): array
    {
        $cache = new FilesystemAdapter();
        $pokemonGenerationList = $cache->getItem('pokemon_generation');
        if (!$pokemonGenerationList->isHit()) {
            $api = $pokeClient->getPokemonGeneration($generation);
            $liste_temp = [];
            $pokemon_list = $api['pokemon_species'];
            foreach ($pokemon_list as $pokemon) {
                $pokemon_details = $pokeClient->getPokemonDetails($pokemon['name']);
                $pokemon_details != [] ? $liste_temp[] = $pokemon_details : null;
            }
            $pokemonGenerationList->set($liste_temp);
            $cache->save($pokemonGenerationList);
        }
        return $pokemonGenerationList->get();
    }
}
