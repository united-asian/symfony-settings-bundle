<?php

namespace UAM\Bundle\SettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class SettingsFormType extends AbstractType
{
    private $settings;

    public function __construct(\Acme\DemoBundle\Helper\SettingsManager $settings)
    {
        $this->settings = $settings;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sampleEmailSetting', 'text', array(
            'label'       => 'Sample email',
            'data'        => $this->settings->get('sampleEmailSetting'),
            'constraints' => new Email()
        ));

        $builder->add('sampleNumberSetting', 'text', array(
            'label'       => 'Sample number',
            'data'        => $this->settings->get('sampleNumberSetting'),
            'constraints' => new Regex(array('pattern' => "/^\d+$/", 'message' => 'This value needs to be numeric'))
        ));

    }

    public function getName()
    {
        return 'settings_form';
    }
}
