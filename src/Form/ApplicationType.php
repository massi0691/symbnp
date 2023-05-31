<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * for basic configuration of fields
     * @param $label
     * @param $placeholder
     * @param array $options
     * @return array
     */
    protected function getConfiguration($label, $placeholder, $options=[])
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeHolder' => $placeholder
            ]
        ],$options) ;

    }
}
