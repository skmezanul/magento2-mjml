<?php

namespace Onilab\MJML\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;

class InstallData implements InstallDataInterface
{
    protected $logger;
    protected $directoryList;

    public function __construct(
        LoggerInterface $logger,
        DirectoryList $directoryList
    )
    {
        $this->logger = $logger;
        $this->directoryList = $directoryList;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $rootPath = $this->directoryList->getRoot();
        $descriptors = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'a']
        ];
        $process = proc_open('npm install mjml', $descriptors, $pipes, $rootPath);
        stream_set_blocking($pipes[2], 0);

        try {
            if (($error = stream_get_contents($pipes[2])) !== false) {
                throw new \Exception('Can not install module Onilab MJML: ' . $error);
            }
            stream_set_blocking($pipes[2], 1);

            $read = [
                $pipes[1], $pipes[2]
            ];
            $write = NULL;
            $except = NULL;

            do {
                $rv = stream_select($read, $write, $except, 1, 0);
                if (!$rv) { //an error occurred while selecting streams
                    throw new \Exception('Can not install module Onilab MJML: error in stream_select');
                }
                if ($rv == 0) { //no streams changed
                    break;
                }
                foreach ($read as $readPipe) {
                    if ($readPipe == $pipes[2]) { //a message in stderr
                        throw new \Exception('Can not install module Onilab MJML: can not install mjml npm package');
                    }
                }
            } while (true);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        proc_close($process);
    }
}