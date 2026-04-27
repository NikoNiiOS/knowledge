<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class CourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Course title'),
            NumberField::new('price', 'Price'),
            
            AssociationField::new('theme', 'Associated theme'), 
            
            DateTimeField::new('createdAt', 'Created at')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Updated at')->hideOnForm(),
            TextField::new('createdBy', 'Created by')->hideOnForm(),
            TextField::new('updatedBy', 'Updated by')->hideOnForm(),
        ];
    }
}
