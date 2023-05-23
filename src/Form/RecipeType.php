<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\IngredientType;
use App\Entity\Recipe;
use App\Entity\RecipeDifficulty;
use App\Entity\RecipeDuration;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RecipeType extends AbstractType
{
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultDifficulty = $this->em->getRepository(RecipeDifficulty::class)->findOneBy([
            'name' => 'Normal'
        ]);
        $defaultDuration = $this->em->getRepository(RecipeDuration::class)->findOneBy([
            'name' => 'Normal'
        ]);
        $builder
            ->add('difficulty', EntityType::class, [
                'class' => RecipeDifficulty::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'difficulty'
                ],
                'data' => $defaultDifficulty
            ])
            ->add('duration', EntityType::class, [
                'class' => RecipeDuration::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'duration'
                ],
                'data' => $defaultDuration
            ])
            ->add('nb_people', NumberType::class, [
                'label' => 'Nombre de personnes',
                'html5' => true,
                'attr' => [
                    'value' => 1,
                    'step' => 1,
                    'min' => 1,
                    'max' => 100,
                    'arrow' => false
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 100,
                        'notInRangeMessage' => 'La valeur doit être comprise entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ]);
            $this->addIngredientsOfTypeField($builder);
            $this->getIngredients($builder);
    }

    public function addIngredientsOfTypeField(FormBuilderInterface $builder)
    {
        $ingredientTypes = $this->em->getRepository(IngredientType::class)->findAll();

        foreach ($ingredientTypes as $type) {
            $builder->add($type->getName(), EntityType::class, [
                'class' => Ingredient::class,
                'choices' => $type->getIngredients(),
                'multiple' => true,
                'expanded' => false,
                'label' => $type->getName(),
                'choice_label' => 'name',
                'mapped' => false,
                'required' => false,
                'attr' => [
                   'class' => 'ingredients',
                ]
            ]);
        }
    }

    public function getIngredients(FormBuilderInterface $builder) {
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $recipe = $event->getData();
            $form = $event->getForm();

            // Initialize ingredients prop
            $ingredients = $recipe->getIngredients() ?? new ArrayCollection();

            // Iterate on all additional fields
            foreach ($form->all() as $name => $field) {
                // Check if field is ingredient type field
                $type = $this->em->getRepository(IngredientType::class)->findOneBy(['name' => $name]);
                if ($type !== null) {
                    $selectedIngredients = $field->getData(); // Get field value(s)
                    foreach ($selectedIngredients as $ingredient) {
                        $ingredients->add($ingredient); // Add each ingredient to ingredients prop
                    }
                }
            }
            foreach ($ingredients as $ingredient) {
                $recipe->addIngredient($ingredient);
            }
            $event->setData($recipe);
        });
    }

    public function validateIngredients(Recipe $recipe, ExecutionContextInterface $context): void
    {
        $ingredientsSelected = count($recipe->getIngredients()->toArray());

        if ($ingredientsSelected === 0) {
            $context
                ->buildViolation('Vous devez sélectionner au moins un ingrédient.')
                ->atPath('ingredients')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'allow_extra_fields' => true,
            'constraints' => [
                new Callback([$this, 'validateIngredients']),
            ],
        ]);
    }
}
