<?php

namespace App\Form;

use App\Entity\Plan;
use App\Entity\User;
use App\Repository\PlanRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('plan', EntityType::class, [
                'class' => Plan::class,
                'label' => 'Plan',
                'choice_label' => 'name',
                'query_builder' => function (PlanRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'placeholder' => 'Sélectionnez un plan',
                'required' => true,
                'data' => $options['user_plan'],

    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'admin_roles' => [],
            'user_plan' => null,
        ]);
    }
}
