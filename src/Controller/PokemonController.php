<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CallApiService; 
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PokemonController extends AbstractController
{
    #[Route('/pokemon/{order_select}', name: 'app_pokemon')]
    public function index(Request $request, PaginatorInterface $paginator, CallApiService $callApiService,string $order_select = 'id'): Response
    {
        $cache = new FilesystemAdapter();
        $generation = $this->getParameter('POKEMON_GENERATION');
        

                
        $productsCount = $cache->getItem('products_count');
        if (!$productsCount->isHit()) {
            $api = $callApiService->getFranceData($generation);
            $liste_temp = [];
            $pokemon_list = $api['pokemon_species'];
            for ($i=0; $i<count($pokemon_list); $i++) {
                $pokemon_details = $callApiService->getFranceDetailsData($pokemon_list[$i]['name']);
                if ($pokemon_details != [] )$liste_temp[]=$pokemon_details;
            }
            $productsCount->set($liste_temp);
            $cache->save($productsCount);
        }
        $liste = $productsCount->get();        
        

        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        
        array_sort_by_column($liste, $order_select);

        $produits = $paginator->paginate(
            $liste, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1),
            12 // Nombre de résultats par page
        );

        return $this->render('pokemon/index.html.twig', [
            'controller_name' => 'PokemonController',
            'data' => $produits
        ]);
    }

    #[Route('/pokemon-details/{name}', name: 'show_pokemon')]
    public function show(CallApiService $callApiService, $name): Response
    {
        $api = $callApiService->getFranceDetailsData($name);
        
        return $this->render('pokemon/show.html.twig', [
            'data' => $api
        ]);
    }
}
