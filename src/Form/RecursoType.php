<?php

namespace App\Form;

use App\Entity\Recurso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('extension')
            ->add('fichero')
            ->add('propietario')
            ->add('favorito')
            ->add('usuarios')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recurso::class,
        ]);
    }
}
