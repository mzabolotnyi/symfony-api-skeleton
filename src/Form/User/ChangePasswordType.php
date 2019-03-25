<?php

namespace App\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', null, [
                'mapped' => false,
                'constraints' => [
                    new UserPassword()
                ],
                'documentation' => [
                    'description' => 'Current password'
                ]
            ])
            ->add('plainPassword', null, [
                'documentation' => [
                    'description' => 'New password'
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
                'allow_extra_fields' => true,
                'validation_groups' => ['Default', 'ChangePassword']
            )
        );
    }
}