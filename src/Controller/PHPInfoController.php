<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PHPInfoController extends AbstractController
{
    #[Route('/phpinfo', name: 'app_phpinfo')]
    public function index(): Response
    {
        ob_start();
        phpinfo();
        $phpInfo = ob_get_clean();

        return new Response($phpInfo);
    }
}
