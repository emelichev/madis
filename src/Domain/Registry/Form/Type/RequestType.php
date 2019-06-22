<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Form\Type;

use App\Domain\Registry\Form\Type\Embeddable\RequestAnswerType;
use App\Domain\Registry\Form\Type\Embeddable\RequestApplicantType;
use App\Domain\Registry\Form\Type\Embeddable\RequestConcernedPeopleType;
use App\Domain\Registry\Model\Request;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object', DictionaryType::class, [
                'label'    => 'registry.request.form.object',
                'name'     => 'registry_request_object',
                'required' => true,
                'expanded' => true,
            ])
            ->add('otherObject', TextType::class, [
                'label'    => 'registry.request.form.other_object',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'label'    => 'registry.request.form.date',
                'required' => true,
            ])
            ->add('reason', TextType::class, [
                'label'    => 'registry.request.form.reason',
                'required' => false,
            ])
            ->add('applicant', RequestApplicantType::class, [
                'label'    => false,
                'required' => true,
            ])
            ->add('concernedPeople', RequestConcernedPeopleType::class, [
                'label'    => false,
                'required' => false,
            ])
            ->add('complete', CheckboxType::class, [
                'label'    => 'registry.request.form.complete',
                'required' => false,
            ])
            ->add('legitimateApplicant', CheckboxType::class, [
                'label'    => 'registry.request.form.legitimate_applicant',
                'required' => false,
            ])
            ->add('legitimateRequest', CheckboxType::class, [
                'label'    => 'registry.request.form.legitimate_request',
                'required' => false,
            ])
            ->add('answer', RequestAnswerType::class, [
                'label'    => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Request::class,
                'validation_groups' => [
                    'default',
                    'request',
                ],
            ]);
    }
}
