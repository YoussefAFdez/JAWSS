<?php

namespace App\Form;

use App\Entity\Imagen;
use App\Entity\Recurso;
use App\Form\RecursoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImagenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recurso', RecursoType::class)
            ->add('resolucion', TextType::class, [
                'label' => 'Resolución:',
                'required' => false,
            ])
        ;
        if ($options['nuevo']) {
            $builder->add('imageFile', VichImageType::class, [
                'label' => 'Subir fichero:',
                'required' => true,
                'allow_delete' => false,
                'download_label' => '',
                'download_uri' => false,
                'attr' => array(
                    'class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400'
                )
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Imagen::class,
            'nuevo' => false,
        ]);
    }
}
