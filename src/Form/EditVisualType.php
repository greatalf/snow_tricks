<?php

namespace App\Form;

use App\Entity\Visual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class EditVisualType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('visual', UrlType::class, [
                'attr' => [
                    'placeholder' => "URL image de la figure"
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visual::class,
            'translation_domain' => 'forms'
        ]);
    }
}
