<?php

namespace App\Form;

use App\Entity\Recurso;
use App\Entity\Tier;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre:'
            ])
            ->add('apellidos', TextType::class, [
                'label' => 'Apellidos:'
            ])
            ->add('nombreUsuario', TextType::class, [
                'label' => 'Nombre de Usuario:'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico:'
            ])
            ->add('clave', PasswordType::class, [
                'label' => 'Contraseña:'
            ])
            ->add('tier', EntityType::class, [
                'label' => 'Tier:',
                'class' => Tier::class,
            ])
            ->add('recursosAccesibles', Select2EntityType::class, [
                'label' => 'Recursos Accesibles por la Persona:',
                'multiple' => true,
                'text_property' => 'nombreUsuario',
                'class' => Recurso::class,
                'minimum_input_length' => 2,
                'required' => false,
                'remote_route' => 'api_recurso_query'
            ])
            ->add('administrador', CheckboxType::class, [
                'label' => 'Administrador'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
