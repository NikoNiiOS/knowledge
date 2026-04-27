<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class LessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Lesson title'),
            TextEditorField::new('content', 'Lesson content'),
            TextField::new('video', 'URL'),
            NumberField::new('price', 'Price'),
            
            AssociationField::new('course', 'Associated course'),
            
            DateTimeField::new('createdAt', 'Created at')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Updated at')->hideOnForm(),
            TextField::new('createdBy', 'Created by')->hideOnForm(),
            TextField::new('updatedBy', 'Updated by')->hideOnForm(),
        ];
    }
}
