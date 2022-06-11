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
                'label' => 'Nombre',
                'attr' => array(
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ),
                'label_attr' => array(
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                )
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => array(
                    'class' => 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ),
                'label_attr' => array(
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                )
            ])
            ->add('extension', TextType::class, [
                'label' => 'Extensión',
                'attr' => array(
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ),
                'label_attr' => array(
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                )
            ])
            ->add('fichero', CheckboxType::class, [
                'label' => 'Es fichero',
                'attr' => array(
                    'class' => 'w-4 h-4 mr-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600'
                ),
                'label_attr' => array(
                    'class' => 'ml-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                )
            ])
            ->add('propietario', EntityType::class, [
                'label' => 'Propietario',
                'class' => Usuario::class,
                'attr' => array(
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ),
                'label_attr' => array(
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                )
            ])
            ->add('favorito', EntityType::class, [
                'label' => 'Favorito',
                'class' => Usuario::class,
                'attr' => array(
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ),
                'label_attr' => array(
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                ),
                'multiple' => true
            ])
            ->add('usuarios', EntityType::class, [
                'label' => 'Usuarios',
                'class' => Usuario::class,
                'attr' => array(
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ),
                'label_attr' => array(
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300'
                ),
                'multiple' => true
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
