<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Category;
use App\Form\VisualType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('title', TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'])
            ->add('content', TextareaType::class)
            ->add('headVisual', UrlType::class, [
                'attr' => [
                    'placeholder' => "URL image de la figure"
                ]
            ])
            ->add('visuals', CollectionType::class, [
                'entry_type' => VisualType::class,
                'allow_add'  => true,
                'allow_delete' => true
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
            'translation_domain' => 'forms'
        ]);
    }
}
