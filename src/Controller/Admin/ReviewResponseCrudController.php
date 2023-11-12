<?php

namespace App\Controller\Admin;

use App\Entity\ReviewResponse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReviewResponseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReviewResponse::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('comment'),
            AssociationField::new('review')
                ->setFormTypeOption('choice_label', 'comment')
                // ->setFormTypeOption('by_reference', false),
        ];
    }

}
