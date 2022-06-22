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
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre:',
                'required' => false,
                'mapped' => false,
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción:',
                'required' => false,
            ])
            ->add('favorito', CheckboxType::class, [
                'label' => 'Favorito',
                'required' => false,
                'mapped' => false,
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
        if ($options['nuevo']) {
            $builder->add('ficheroFile', VichFileType::class, [
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
            'data_class' => Recurso::class,
            'nuevo' => false,
        ]);
    }
}
