<?php

namespace App\Form;

use App\Entity\Export;
use App\Entity\Ticket;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('allTickets', EntityType::class, array(
                'class' => Ticket::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.export IS NULL')
                        ->orderBy('u.project');
                },
                'choice_label' => 'content',
                'multiple' => true,
                'expanded' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Export::class,
        ]);
    }
}
