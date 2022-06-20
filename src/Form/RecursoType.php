<?php

namespace App\Form;

use App\Entity\Recurso;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

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
            ->add('favorito', EntityType::class, [
                'label' => 'Favorito',
                'class' => Usuario::class,
                'multiple' => true,
                'required' => false,
            ])
            ->add('usuarios', Select2EntityType::class, [
                'label' => 'Usuarios con Acceso:',
                'multiple' => true,
                'text_property' => 'nombreUsuario',
                'class' => Usuario::class,
                'minimum_input_length' => 2,
                'required' => false,
                'remote_route' => 'api_usuario_query'
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
