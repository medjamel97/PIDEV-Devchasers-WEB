<?php

namespace App\Controller\back_end_admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_admin/")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("")
     */
    public function dashboard()
    {
        return $this->redirect("/espace_admin/candidat");
    }
}
