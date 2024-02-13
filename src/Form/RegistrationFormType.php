<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $abo1 = new Abonnement();
        $abo2 = new Abonnement();

        $abo1->setNom("Preteur");
        $abo2->setNom("Emprunteur");

        $abo1->setPrix("10");
        $abo2->setPrix("10");

        $abo1->setNiveau("1");
        $abo2->setNiveau("2");

        $builder
            ->add('username')
            ->add('email')
            ->add('abonnement', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Prêteur' => $abo1,
                    'Emprunteur' => $abo2,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un abonnement.',
                    ]),
                ],
                'attr' => [
                    'class' => 'custom-choice-box', // Ajoutez ici votre classe personnalisée
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('first_name')
            ->add('surname')
        ;

        

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
