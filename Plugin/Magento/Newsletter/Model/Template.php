<?php

namespace Onilab\MJML\Plugin\Magento\Newsletter\Model;

use Onilab\MJML\Helper\Compiler;

class Template
{
    protected $compiler;

    public function __construct(
        Compiler $compiler
    )
    {
        $this->compiler = $compiler;
    }

    public function afterGetProcessedTemplate(
        \Magento\Newsletter\Model\Template $subject,
        $result
    ) {
        if ($subject->isChildTemplate()) {
            return $result;
        }
        $compiled = $this->compiler->compile($result);

        return !empty($compiled) ? $compiled : $result;
    }
}
