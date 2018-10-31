<?php

namespace AppBundle\Command;

use AppBundle\Wrapper\BashProcessWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zxing\QrReader;

/**
 * Class QrCodeReadPdfCommand
 *
 * @package AppBundle\Command
 */
class QrCodeReadPdfCommand extends ContainerAwareCommand
{
    /**
     * @var BashProcessWrapper
     */
    protected $bashProcessWrapper;

    /**
     * QrCodeReadPdfCommand constructor.
     *
     * @param BashProcessWrapper $bashProcessWrapper
     * @param null               $name
     */
    public function __construct(BashProcessWrapper $bashProcessWrapper, $name = null)
    {
        parent::__construct($name);
        $this->bashProcessWrapper = $bashProcessWrapper;
    }

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('qr-code:read:pdf')
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

        $initialTime = microtime(true);

        $imagePath = $path . '_pages' . DIRECTORY_SEPARATOR;

        // Create directory to save PDF papers if it wasn't existed
        if (!file_exists($imagePath)) {
            mkdir($imagePath);
        }

        // Split PDF pages into compressed images
        $this->bashProcessWrapper->splitPdfPages($path, $imagePath . 'page.jpg');

        $output->writeln(['PDF split/convert process: ', (microtime(true) - $initialTime)]);

        // Get all images that were splitted from the original PDF and sort them by name
        $generatedImages = scandir($imagePath);
        natsort($generatedImages);

        foreach ($generatedImages as $generatedImage) {
            if (!in_array($generatedImage, ['.', '..'])) {
                $qrcode = new QrReader($imagePath . DIRECTORY_SEPARATOR . $generatedImage);
                $text = $qrcode->text(); //return decoded text from QR Code
                $output->writeln($text);
            }
        }

        $output->writeln((microtime(true) - $initialTime));
    }
}
