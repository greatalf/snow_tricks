<?php

namespace App\Form;

use App\Entity\Visual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VisualType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'attr' => [
                    'placeholder' => "URL du media"
                ]
            ])
            ->add('caption', TextType::class, [
                'attr' => [
                    'placeholder' => "Titre du media"
                ]
            ])
            ->add('visualKind', ChoiceType::class, [
                'choices' => $this->getChoices()
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

    public function getChoices()
    {
        return array_flip(Visual::VISUALKIND); 
    }
}
