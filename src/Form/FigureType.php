<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Category;
use App\Entity\Visual;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['label' => 'Titre'])
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name', 'label' => 'Catégorie'])
            ->add('content', null, ['label' => 'Description']);
            // ->add('visuals', FileType::class, [
            //     'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
