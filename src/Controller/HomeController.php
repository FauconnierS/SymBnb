<?php

namespace App\Controller ;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class HomeController extends Controller {

    /**
     * montre la page qui dit bonjour
     *
     * @Route("/hello/{prenom}/age/{age}", name="hello")
     * @Route("/hello", name="hello_base")
     * @Route("/hello/{prenom}", name="hello_prenom")
     * 
     * @return void
     */
    public function hello($prenom ="inconnu", $age = 0){
        return $this -> render(
            "hello.html.twig",
            [
                'title_page' => "Hello",
                'prenom' => $prenom,
                'age' => $age
            ]
        );
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home () {

        $prenoms = ["Steeve" => 29, "Elodie" => 31, "Floriant" => 32];

      return $this->render(
          "home.html.twig",
          [
              'title_page' => "Home",
              'title' => "Bonjour les amis !",
              'age' => 12,
              'tableau' => $prenoms 
          ]
      );
    }

}

?>