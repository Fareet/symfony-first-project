<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\DataTransformer\DatetimeTransformer;

class NoteType extends AbstractType
{
    public function __construct(
        private DatetimeTransformer $transformer,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'required' => true
            ])
            ->add('text',TextareaType::class, [
                'required' => true
            ])
            ->add('datetime', HiddenType::class, [
                'required'   => false,
                'attr' => array('value' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')),
            ])
            ->get('datetime')->addModelTransformer($this->transformer);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
