<?php

namespace Onilab\MJML\Plugin\Magento\Email\Model;

use Onilab\MJML\Helper\Compiler;

class Template
{
    protected $compiler;

    public function __construct(
        Compiler $compiler,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->compiler = $compiler;
    }

    public function afterGetProcessedTemplate(
        \Magento\Email\Model\Template $subject,
        $result
    ) {
        if ($subject->isChildTemplate()) {
            return $result;
        }
        $compiled = $this->compiler->compile($result);

        return !empty($compiled) ? $compiled : $result;
    }
}
