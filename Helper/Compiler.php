<?php

namespace Onilab\MJML\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

class Compiler extends AbstractHelper
{
    protected $directoryList;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    public function compile($MJMLContent)
    {
        $cmd = $this->getMjmlBinPath() . ' --stdin --stdout ';

        $descriptors = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['file', $this->getLogFilePath(), 'a']
        ];

        $process = proc_open($cmd, $descriptors, $pipes, $this->getRootPath());

        fwrite($pipes[0], $MJMLContent);
        fclose($pipes[0]);

        $compiledContent = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        proc_close($process);

        return $compiledContent;
    }

    protected function getLogFilePath()
    {
        return $this->directoryList->getPath(DirectoryList::LOG) . '/onilab-mjml-compiler-errors.log';
    }

    protected function getRootPath()
    {
        return $this->directoryList->getRoot();
    }

    protected function getNodeModulesPath()
    {
        return $this->getRootPath() . '/node_modules';
    }

    protected function getMjmlBinPath()
    {
        return $this->getNodeModulesPath() . '/.bin/mjml';
    }
}