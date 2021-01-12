<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService {
    
    private $entityClass ; 

    private $limit = 10;

    private $currentPage = 1 ;

    private $manager; 

    private $route ;

    private $twig ; 

    private $templatePath ;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath){
        $this -> manager = $manager;
        $this -> twig = $twig;
        $this -> route = $request -> getCurrentRequest() -> attributes -> get('_route');
        $this -> templatePath = $templatePath ;
    }

    
    public function setEntityClass ($entityClass){
         $this -> entityClass = $entityClass ;
        return $this;
    } 
    
    public function getEntityClass(){
        return $this -> entityClass ;
    }
    
    public function setLimit($limit){
        $this -> limit = $limit; 
        return $this ; 
    }
    
    public function getLimit(){
        return $this -> limit ;
    }
    
    public function setCurrentPage($currentPage) {
        $this -> currentPage = $currentPage;
        return $this ; 
    }
    
    public function getCurrentPage(){
        return $this -> currentPage ;
    }

    public function setRoute($route){
        $this -> route = $route ;

        return $this ; 
    }

    public function getRoute() {
        return $this -> route ;
    }

    public function setTemplatePath($templatePath){
        $this -> templatePath = $templatePath ;
        return $this ;
    }

    public function getTemplatePath(){
        return $this -> templatePath ;
    }

    public function getData(){
        if(empty($this -> entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! utilisez la méthode setEntityClass() de votre PaginationService");
        }
        $offset = $this -> currentPage * $this -> limit - $this -> limit ;
        $repo = $this -> manager -> getRepository($this -> entityClass);
        $data = $repo -> findBy([], [] , $this -> limit , $offset);
    
        return $data ; 
    }

    public function getPages(){
        if(empty($this -> entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! utilisez la méthode setEntityClass() de votre PaginationService");
        }
        $repo = $this -> manager -> getRepository($this -> entityClass);
        $total = count($repo -> findAll());
        $pages = ceil($total/ $this -> limit);

        return $pages;
    }

    public function display(){
        $this -> twig -> display( $this -> templatePath , [
            'page' => $this -> currentPage,
            'pages' => $this -> getPages(),
            'path' => $this -> route 
        ]);
    }
}
