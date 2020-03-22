<?php declare(strict_types=1);


namespace App\Form;

use MessageInfo\NumberAPIDataProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'negativ' => false,
                    'positiv' => true,
                ],
                'label' => 'Status',
                'placeholder' => 'select test result: positiv / negativ',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NumberAPIDataProvider::class,
        ]);
    }
}
