<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

final class CharacterType extends AbstractType
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', null, [
                'attr' => ['autofocus' => true],
                'label' => 'Name',
            ])->add('mass', null, [
                'label' => 'Mass',
            ])->add('height', null, [
                'label' => 'Height',
            ])->add('gender', null, [
                'label' => 'Gender',
            ])->add('picture', FileType::class, [
                'label' => 'Picture',
                'data_class' => null
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}
