<?php

namespace App\Form;

use App\Entity\Recurso;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre:'
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción:',
            ])
            ->add('extension', TextType::class, [
                'label' => 'Extensión:',
            ])
            ->add('fichero', CheckboxType::class, [
                'label' => 'Es fichero',
                'required' => false,
            ])
            ->add('propietario', EntityType::class, [
                'label' => 'Propietario',
                'class' => Usuario::class,
            ])
            ->add('favorito', EntityType::class, [
                'label' => 'Favorito',
                'class' => Usuario::class,
                'multiple' => true,
                'required' => false,
            ])
            ->add('usuarios', EntityType::class, [
                'label' => 'Usuarios',
                'class' => Usuario::class,
                'multiple' => true,
                'required' => false,
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recurso::class,
        ]);
    }
}
