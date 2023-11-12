<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFilters(Filters $filters): Filters //ajout d'un bouton de filtre et définition de ce filtre
    {
        $roles = ['Admin'=>'ROLE_ADMIN', 'Restorer'=>'ROLE_RESTORER', 'User'=>''];
        return $filters
            ->add(ArrayFilter::new('roles') //filtre sur un tableau
            ->setChoices($roles))
        ;
    }





    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Identité', 'fa-solid fa-user'), //ajout d'onglets
            IdField::new('id')->onlyOnIndex(), // ce champs ne sera pas visible dans le formulaire de modification
            //
            //ATTENTION SI ON FAIT DES ONGLETS, IL FAUDRA TOUJOURS METTRE L'ID DANS UN ONGLET SINON CERTAINS LIENS NE FONCTIONNERONT PAS ! ON
            //
            FormField::addColumn(4), //ajout d'une colonne (taille 4 colonnes)
            TextField::new('Lastname'),
            ChoiceField::new('roles')
                ->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'Restorer' => 'ROLE_RESTORER',
                'User' => 'ROLE_USER'
                ])
                ->allowMultipleChoices()
                ->autocomplete(),
            FormField::addColumn(7),
            EmailField::new('Email'), //défini la taille de cet input à 7 colonnes
            FormField::addTab('Adresse', 'fa-solid fa-house'), //ajout d'onglets
            FormField::addColumn(12) //ajout d'une colonne (taille 12 colonne)
                ->addCssClass(' d-flex justify-content-between'), //ajout de class de css (bootstrap)
            AssociationField::new('city')->setColumns(3), //défini la taille de cet input à 3 colonnes
            TextField::new('Address')->onlyOnForms()->setColumns(8), //défini la taille de cet input à 8 colonnes
            FormField::addTab('Restaurant et avis', 'fa-solid fa-star'), //ajout d'onglets
            AssociationField::new('reviews'),
            AssociationField::new('restaurant'),
        ];
        // yield IdField::new('id');
        // yield EmailField::new('Email');
        // yield TextField::new('Lastname');
        // yield TextField::new('Address');
        // yield AssociationField::new('city');
        // yield CollectionField::new('reviews')->setEntryType(ReviewType::class);
        // yield ArrayField::new('roles');
    }

}
