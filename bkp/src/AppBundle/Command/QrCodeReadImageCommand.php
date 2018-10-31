<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zxing\QrReader;

/**
 * Class QrCodeReadImageCommand
 *
 * @package AppBundle\Command
 */
class QrCodeReadImageCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('qr-code:read:image')
            ->setDescription('...')
            ->addArgument('path', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        // $initialTime = microtime(true);
        $qrcode = new QrReader($path);
        $text = $qrcode->text(); //return decoded text from QR Code
        $output->writeln($text);
        // $output->writeln((microtime(true) - $initialTime));
    }
}
