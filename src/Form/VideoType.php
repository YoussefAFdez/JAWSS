<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use App\Entity\Recurso;
use App\Form\RecursoType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recurso', RecursoType::class)
            ->add('duracion', TextType::class, [
                'label' => 'Duración:',
                'required' => false,
            ])
        ;

        if ($options['nuevo']) {
            $builder->add('videoFile', VichFileType::class, [
                'label' => 'Subir fichero:',
                'required' => true,
                'allow_delete' => false,
                'attr' => array(
                    'class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400'
                ),
                'download_uri' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'nuevo' => false
        ]);
    }
}
