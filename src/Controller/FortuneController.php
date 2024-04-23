<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\FortuneCookie;
use App\Model\CategoryFortuneStats;
use App\Repository\CategoryRepository;
use App\Repository\FortuneCookieRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(Request $request,CategoryRepository $categoryRepository): Response
    {
        //findBy([],['name' => 'ASC']); el segundo parametro [] de esta consulta se basa en el orden por ejemplo asc o desc
        //el primero es para encontrar uno en especifico, vamos bien el criterio
        //al find (a secas) hay que pasarle un int pero debe ser como variable
        //en el base.html.twig el form tiene un name q, entonces si el valor no es null se metera en el if
        $searchTerm = $request->query->get('q');
        if ($searchTerm){
            $categories = $categoryRepository->search($searchTerm);
        }
        else{
            $categories = $categoryRepository->findAllOrdered();
//            $categories = $categoryRepository->findBy(['name' =>['Job', 'Love']], ['name' => 'ASC']);
//            $categories = $categoryRepository->findBy(['name' =>'Job','id' =>'1'], ['name' => 'ASC']);

        }
        return $this->render('fortune/homepage.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(int $id, FortuneCookieRepository $fortuneCookie, CategoryRepository $categoryRepository): Response
    {
        /* return $this->render('fortune/showCategory.html.twig',[
            'category' => $category,
            'fortunesPrinted' => $result['fortunesPrinted'],
            'fortunesAverage' => $result['fortunesAverage']
            ahora en el showCategory.twig solamente tendria que
            poner {{ fortunesPrinted }}

        ]);*/
        $category = $categoryRepository->findWithFortunesJoin($id);
        $result = $fortuneCookie->countFortuneCookieModelAvg($category);
        //accedemos a las propiedades que tiene nuestro model
        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category,
//             para el model categoryFortuneStats se puede acceder a sus propiedades asi pero
                //el metodo countFortune deberia devolver un objeto CategoryFortunesStats
            'fortunePrinted' => $result->fortunePrinted,
            'fortuneAverage' => $result->fortuneAverage,
//            'result' => $result
        ]);
    }
}
