<?php

namespace App\Form;

use App\Entity\BlogPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\PostCategory;

class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, ['label' => 'Tytuł'])
            ->add('html', CKEditorType::class, ['label' => 'Zawartość',
                                                'config' => ['uiColor' => '#ffffff'],
                                                'sanitize_html' => true])
            ->add('image', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '5120K',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Załącz obrazek *.png, *.jpg, *.webp lub *.svg!',
                    ])
                    
                ],
                'mapped' => false,
                'label' => 'Zdjęcie postu'
            ])
            ->add('category', EntityType::class, [
                'class' => PostCategory::class,
                'choice_label' => 'name',
                'choice_name' => 'id',
                'label' => 'Kategoria'
            ])
            ->add('submit', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}
