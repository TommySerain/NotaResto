<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class RestaurantCrudController extends AbstractCrudController
{

    public function configureCrud(Crud $crud): Crud //configuration du crud
    {
        return $crud
            ->setEntityLabelInSingular('Restaurant')
            ->setEntityLabelInPlural('Restaurants')
            ->setSearchFields(['name', 'user.lastname']) //configuration de la barre de recherche, ici on pourra faire une recherche pour la collonne name et pour la colonne user par son lastname
            ->setDefaultSort(['id' => 'DESC']) // définition de la colonne de tri et de son sens 
        ;
    }

    public function configureFilters(Filters $filters): Filters //ajout d'un bouton de filtre et définition de ce filtre
    {
        return $filters
            ->add(EntityFilter::new('user')) //filtre sur une entité
        ;
    }
    public static function getEntityFqcn(): string
    {
        return Restaurant::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        // return [
        //     IdField::new('id'),
        //     TextField::new('Name'),
        //     TextField::new('Description'),
        //     TextField::new('Address'),
        // ];
        yield FormField::addColumn(4)
        ->addCssClass("border border-2 rounded-1 mx-auto");
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name');
        yield AssociationField::new('city');
        yield AssociationField::new('user');
        yield FormField::addColumn(7)
            ->addCssClass("border border-2 rounded-1 mx-auto");
        yield AssociationField::new('reviews');
    }
    
}
