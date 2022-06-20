<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre:',
                'attr' => array(
                    'placeholder' => 'Nombre'
                ),
            ])
            ->add('apellidos', TextType::class, [
                'label' => 'Apellidos:',
                'attr' => array(
                    'placeholder' => 'Apellidos'
                ),
            ])
            ->add('nombreUsuario', TextType::class, [
                'label' => 'Usuario:',
                'attr' => array(
                    'placeholder' => 'Usuario'
                ),
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico: ',
                'attr' => array(
                    'placeholder' => 'nombre@correo.com'
                ),
            ])
            ->add('clave', RepeatedType::class, [
                'label' => 'Contraseña',
                'type' => PasswordType::class,

                'mapped' => false,
                'invalid_message' => 'Las contraseñas no coinciden',
                'first_options' => [
                    'label' => 'Contraseña: ',
                    'constraints' => [
                        new NotBlank([
                            'groups' => ['password']
                        ])
                    ],
                    'attr' => array(
                        'placeholder' => '••••••••'
                    ),
                ],
                'second_options' => [
                    'label' => 'Repite contraseña: ',
                    'attr' => array(
                        'placeholder' => '••••••••'
                    ),
                ]
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
