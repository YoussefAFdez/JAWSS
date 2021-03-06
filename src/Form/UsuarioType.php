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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['cambioDatos']) {
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
                ->add('clave', RepeatedType::class, [
                    'label' => 'Contraseña',
                    'type' => PasswordType::class,
                    'required' => false,
                    'mapped' => false,
                    'invalid_message' => 'Las contraseñas no coinciden',
                    'first_options' => [
                        'label' => 'Contraseña: ',
                        'constraints' => [
                            new NotBlank([
                                'groups' => ['password'],
                                'message' => 'La contraseña no puede estar vacía',
                            ]),
                            new Length([
                                'min' => 6,
                                'max' => 50,
                                'minMessage' => 'La contraseña debe de tener al menos 6 caracteres',
                                'maxMessage' => 'La contraseña puede tener como mucho 50 caracteres',
                            ])
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Repite contraseña: '
                    ]
                ])
                ->add('tier', EntityType::class, [
                    'label' => 'Tier:',
                    'class' => Tier::class,
                ])
                ->add('administrador', CheckboxType::class, [
                    'label' => 'Administrador',
                    'required' => false,
                ])
            ;
        } else {
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
                ->add('claveAntigua', PasswordType::class, [
                    'label' => 'Contraseña actual',
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new UserPassword([
                            'message' => 'La contraseña introducida debe de coincidir con la del usuario actual.'
                        ]),
                        new NotBlank([
                            'message' => 'No puedes dejar este campo en blanco.'
                        ])
                    ]
                ])->add('clave', RepeatedType::class, [
                    'label' => 'Contraseña',
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'invalid_message' => 'Las contraseñas no coinciden',
                    'first_options' => [
                        'label' => 'Contraseña: ',
                        'constraints' => [
                            new NotBlank([
                                'groups' => ['password'],
                                'message' => 'La contraseña no puede estar vacía',
                            ]),
                            new Length([
                                'min' => 6,
                                'max' => 50,
                                'minMessage' => 'La contraseña debe de tener al menos 6 caracteres',
                                'maxMessage' => 'La contraseña puede tener como mucho 50 caracteres',
                            ])
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Repite contraseña: '
                    ]
                ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'cambioDatos' => false,
        ]);
    }
}
