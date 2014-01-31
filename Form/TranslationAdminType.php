<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', 'hidden');
        $builder->add('text', 'textarea');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translation';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\Kunstmaan\TranslatorBundle\Entity\Translation',
        ));
    }
}
