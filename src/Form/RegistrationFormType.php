<?php

namespace App\Form;

use App\Entity\User;

use App\Entity\Abonnement;
use App\Repository\AbonnementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class RegistrationFormType extends AbstractType
{

    private $abonnementRepository;

    public function __construct(AbonnementRepository $abonnementRepository)
    {
        $this->abonnementRepository = $abonnementRepository;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $abonnements = $this->abonnementRepository->findAll();

        $choices = [];
        foreach ($abonnements as $abonnement) {
            $choices[$abonnement->getNom()] = $abonnement;
        }

        $builder
            ->add('username', null, [

                'required'=>false,
                'attr' => [

                    'placeholder' => "Entrez votre pseudo",
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre nom.', // Message d'erreur si le champ est vide
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [

                    'placeholder' => "Entrez votre mail"
                ],
                'required'=>false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre adresse e-mail.',
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                        'mode' => 'strict',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas au bon format.',
                    ]),
                ],
            ])
            ->add('abonnement', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => $choices,
                'choice_label' => function ($abonnement) {
                    return $abonnement->getNom();
                },
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
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => "Entrez votre mot de passe"],
                'required'=>false,
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
            ->add('first_name', null, [

                'attr' => [

                    'placeholder' => "Entrez votre prénom"
                ],
            ])
            ->add('surname', null, [

                'attr' => [

                    'placeholder' => "Entrez votre nom"
                ],
            ])
        ;

        

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
