<?php

namespace App\Form;

use App\Entity\Modelo3D;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Modelo3DType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('material')
            ->add('relleno')
            ->add('resolucion')
            ->add('soportes')
            ->add('url')
            ->add('recurso')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Modelo3D::class,
        ]);
    }
}
