<?php declare(strict_types=1);


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class App extends AbstractController
{
    public function home(): Response
    {
        return $this->render('app/home.html.twig', [
        ]);
    }
}
