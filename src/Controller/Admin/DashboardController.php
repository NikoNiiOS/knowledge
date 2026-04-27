<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ThemeCrudController;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Lesson;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ThemeCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Knowledge Learning');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('E-learning');
        yield MenuItem::linkToCrud('Themes', 'fas fa-folder', Theme::class);
        yield MenuItem::linkToCrud('Courses', 'fas fa-graduation-cap', Course::class);
        yield MenuItem::linkToCrud('Lessons', 'fas fa-book', Lesson::class);
        
        yield MenuItem::section('Admin');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
    }
}
