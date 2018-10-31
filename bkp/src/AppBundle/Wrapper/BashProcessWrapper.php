<?php
/**
 * Created by PhpStorm.
 * User: amir
 * Date: 10/24/18
 * Time: 12:33 PM
 */

namespace AppBundle\Wrapper;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class BashProcessWrapper
 *
 * @package AppBundle\Wrapper
 */
class BashProcessWrapper
{
    protected function runCommand($command)
    {
        $process = new Process($command);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Function that splits PDF document pages into images
     *
     * Requires Imagick to be installed on OS level, Check imagick privacy.xml if experienced an error during conversion
     *
     * @param string $pdfPath
     * @param string $imagePath
     */
    public function splitPdfPages($pdfPath, $imagePath)
    {
        // -density is very critical parameter, increasing it will increase the overall conversion time, and decreasing
        // it may distort the output images
        $this->runCommand('convert -density 150 -quality 20 ' . $pdfPath . ' ' . $imagePath);
    }

    /**
     * @param $imagePath
     */
    public function readQrFromImage($imagePath)
    {
        return $this->runCommand('zbarimg ' . $imagePath);
    }
}
