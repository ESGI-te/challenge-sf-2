<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => ['readonly' => true],
                'disabled' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    'Authenticated' => 'IS_FULLY_AUTHENTICATED'
                ],
                'data' => $options['admin_roles'],
            ])
            ->add('nb_toke', IntegerType::class, [
                'label' => 'Nombre de toque',
                'attr' => ['min' => 0],
                'required' => true,
                'data' => $options['nb_toke'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'admin_roles' => [],
            'nb_toke' => 0,
        ]);
    }
}
