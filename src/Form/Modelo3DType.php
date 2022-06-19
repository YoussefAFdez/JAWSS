<?php

namespace App\Form;

use App\Entity\Modelo3D;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use App\Form\RecursoType;

class Modelo3DType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recurso', RecursoType::class)
            ->add('material', TextType::class, [
                'label' => 'Material:'
            ])
            ->add('relleno', NumberType::class, [
                'label' => 'Relleno:'
            ])
            ->add('resolucion', TextType::class, [
                'label' => 'Resolución'
            ])
            ->add('soportes', CheckboxType::class, [
                'label' => 'Soportes',
                'required' => false,
            ])
            ->add('url', TextType::class, [
                'label' => 'URL: '
            ])
            ->add('modeloFile', VichFileType::class, [
                'label' => 'Subir fichero:',
                'required' => false,
                'allow_delete' => false,
                'download_label' => '',
                'download_uri' => false,
                'attr' => array(
                    'class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400'
                ),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Modelo3D::class,
        ]);
    }
}
