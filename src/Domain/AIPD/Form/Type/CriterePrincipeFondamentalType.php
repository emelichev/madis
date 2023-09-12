<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CriterePrincipeFondamentalType extends AbstractType
{
    protected string $maxSize;

    public function __construct(string $maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', SanitizeTextFormType::class)
            ->add('labelLivrable', SanitizeTextFormType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('reponse', DictionaryType::class, [
                'name' => 'reponse_critere_fondamental',
            ])
            ->add('isVisible', CheckboxType::class, [
                'label'    => false,
                'required' => false,
            ])
            ->add('texteConformite', SanitizeTextFormType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('texteNonConformite', SanitizeTextFormType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('texteNonApplicable', SanitizeTextFormType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('justification', SanitizeTextFormType::class, [
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('deleteFile', HiddenType::class, [
                'data' => 0,
            ])
            ->add('fichierFile', FileType::class, [
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
//                        'mimeTypesMessage' => 'aipd.critere_principe_fondamental.fichier.file',
                        'groups' => ['default'],
                    ]),
                ],
            ])
        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => CriterePrincipeFondamental::class,
                'validation_groups' => [
                    'default',
                    'aipd',
                ],
            ]);
    }
}
