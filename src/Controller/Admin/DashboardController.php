<?php

/* Controller : "Chef d'orchestre de l'application" qui reçoit les requêtes et gère les
interactions entre les utilisateurs et le modèle. */

// NB : "App" est égal à "src" : src/Controller/DashboardController.php.
// je créé un namespace qui correspond au chemin vers cette classe.
// Cela va permettre à Symfony d'auto-charger ma classe.
namespace App\Controller\Admin;

/* Pour pouvoir utiliser la classe dans mon code,
je fais un "use" vers le namespace (qui correspond au chemin) de la classe "Route".
Cela revient à faire un import ou un require en PHP*/
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function adminDashboard()
    {
        return $this->render('admin/home/dashboard.html.twig');
    }
}