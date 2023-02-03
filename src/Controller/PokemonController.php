<?php

namespace App\Controller;

use App\Service\PokeClient; 
use App\Service\PokemonSorter; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PokemonController extends AbstractController
{
    #[Route('/pokemon/{order_select}', name: 'app_pokemon')]
    public function index(Request $request, PaginatorInterface $paginator, PokeClient $pokeClient, PokemonSorter $pokemonSorter,string $order_select = 'id'): Response
    {

        /* $generation = $this->getParameter('POKEMON_GENERATION');
        $cache = new FilesystemAdapter();
        $productsCount = $cache->getItem('products_count');
        if (!$productsCount->isHit()) {
            $api = $pokeClient->getPokemonGeneration($generation);
            $liste_temp = [];
            $pokemon_list = $api['pokemon_species'];
            for ($i=0; $i<count($pokemon_list); $i++) {
                $pokemon_details = $pokeClient->getPokemonDetails($pokemon_list[$i]['name']);
                if ($pokemon_details != [] )$liste_temp[]=$pokemon_details;
            }
            $productsCount->set($liste_temp);
            $cache->save($productsCount);
        }
        $liste = $productsCount->get();  */  
        
        $generation = $this->getParameter('POKEMON_GENERATION');

        $api = $pokeClient->getPokemonGeneration($generation);
        $liste_temp = [];
        $pokemon_list = $api['pokemon_species'];
        foreach ($pokemon_list as $pokemon)
        {
            $pokemon_details = $pokeClient->getPokemonDetails($pokemon['name']);
            $pokemon_details != [] ? $liste_temp[]=$pokemon_details : null;
        }
            
        

        $liste_valide = $pokemonSorter->getPokemonSorter($liste_temp, $order_select);

        $produits = $paginator->paginate(
            $liste_valide, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1),
            12 // Nombre de résultats par page
        );

        return $this->render('pokemon/index.html.twig', [
            'controller_name' => 'PokemonController',
            'data' => $produits
        ]);
    }

    #[Route('/pokemon-details/{name}', name: 'show_pokemon')]
    public function show(PokeClient $pokeClient, $name): Response
    {
        $api = $pokeClient->getPokemonDetails($name);
        
        return $this->render('pokemon/show.html.twig', [
            'data' => $api
        ]);
    }
}
