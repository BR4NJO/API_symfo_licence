<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\VideoGame;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('releaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('description')
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VideoGame::class,
        ]);
    }
}
