<?php

namespace AppBundle\Controller;

use AppBundle\Wrapper\BashProcessWrapper;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zxing\QrReader;

/**
 * Class DefaultController
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @var BashProcessWrapper
     */
    protected $bashProcessWrapper;

    /**
     * DefaultController constructor.
     *
     * @param BashProcessWrapper $bashProcessWrapper
     */
    public function __construct(BashProcessWrapper $bashProcessWrapper)
    {
        $this->bashProcessWrapper = $bashProcessWrapper;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/qr/generate", name="test_generate_qr_action")
     *
     * @return Response
     */
    public function generateQr()
    {
        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_JPG,
            'eccLevel'   => QRCode::ECC_L,
        ]);
        $qr = new QRCode($options);
        $orderDetails = [
            'order_id' => 12345,
            'page_number' => 3
        ];
        $orderDetails = json_encode($orderDetails);

        // 2D QR Code
        echo '<img src="' . $qr->render($orderDetails) . '" />';

        // Generating datamatrix code
        $datamatrix = new \TCPDF2DBarcode('123456', 'DATAMATRIX');
        $datamatrixSVG = $datamatrix->getBarcodeSVGcode(20, 20);
        echo $datamatrixSVG;

        die;
    }

    /**
     * @Route("/pdf/generate", name="test_generate_pdf_action")
     *
     * @return Response
     */
    public function generatePdfWithQrCode()
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Amir Iskander');
        $pdf->SetTitle('TCPDF QR Code Example');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide, qr-code');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 027', PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------
        // set a barcode on the page footer
        $pdf->setBarcode(date('Y-m-d H:i:s'));
        // set font
        $pdf->SetFont('helvetica', '', 11);


        // add a page
        $pdf->AddPage();
        // print a message
        $txt = "You can also export 1D barcodes in other formats (PNG, SVG, HTML). Check the examples inside the barcodes directory.\n";
        $pdf->MultiCell(70, 50, $txt, 0, 'J', false, 1, 125, 30, true, 0, false, true, 0, 'T', false);
        $pdf->SetY(30);
        // -----------------------------------------------------------------------------
        $pdf->SetFont('helvetica', '', 10);
        // define barcode style
        $style = array(
            'position'     => '',
            'align'        => 'C',
            'stretch'      => false,
            'fitwidth'     => true,
            'cellfitalign' => '',
            'border'       => false,
            'hpadding'     => 'auto',
            'vpadding'     => 'auto',
            'fgcolor'      => array(0, 0, 0),
            'bgcolor'      => false, //array(255,255,255),
            'text'         => true,
            'font'         => 'helvetica',
            'fontsize'     => 8,
            'stretchtext'  => 4,
        );
        // PRINT VARIOUS 1D BARCODES
        // CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
        $pdf->Cell(0, 0, 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9', 0, 1);
        $pdf->write1DBarcode('CODE 39', 'C39', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // CODE 39 + CHECKSUM
        $pdf->Cell(0, 0, 'CODE 39 + CHECKSUM', 0, 1);
        $pdf->write1DBarcode('CODE 39 +', 'C39+', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // CODE 39 EXTENDED
        $pdf->Cell(0, 0, 'CODE 39 EXTENDED', 0, 1);
        $pdf->write1DBarcode('CODE 39 E', 'C39E', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // CODE 39 EXTENDED + CHECKSUM
        $pdf->Cell(0, 0, 'CODE 39 EXTENDED + CHECKSUM', 0, 1);
        $pdf->write1DBarcode('CODE 39 E+', 'C39E+', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // CODE 93 - USS-93
        $pdf->Cell(0, 0, 'CODE 93 - USS-93', 0, 1);
        $pdf->write1DBarcode('TEST93', 'C93', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // Standard 2 of 5
        $pdf->Cell(0, 0, 'Standard 2 of 5', 0, 1);
        $pdf->write1DBarcode('1234567', 'S25', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // Standard 2 of 5 + CHECKSUM
        $pdf->Cell(0, 0, 'Standard 2 of 5 + CHECKSUM', 0, 1);
        $pdf->write1DBarcode('1234567', 'S25+', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // Interleaved 2 of 5
        $pdf->Cell(0, 0, 'Interleaved 2 of 5', 0, 1);
        $pdf->write1DBarcode('1234567', 'I25', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Ln();
        // Interleaved 2 of 5 + CHECKSUM
        $pdf->Cell(0, 0, 'Interleaved 2 of 5 + CHECKSUM', 0, 1);
        $pdf->write1DBarcode('1234567', 'I25+', '', '', '', 18, 0.4, $style, 'N');


        // QR Code
        $pageCounter = 0;
        $orderId     = 11111;
        $pdf->AddPage();


        // QRCODE,M : QR-CODE Medium error correction
        $pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,M', 20, 30, 50, 50, $style, 'N');
        $pdf->Text(20, 85, 'QRCODE M');

        // QRCODE,L : QR-CODE Low error correction
        $qrCodeData = json_encode(['order_id' => $orderId, 'page_number' => ++$pageCounter]);
        $pdf->write2DBarcode($qrCodeData, 'QRCODE,L', 20, 90, 25, 25, $style, 'N');
        $pdf->Text(20, 25, 'QRCODE L');

        // QRCODE,Q : QR-CODE Better error correction
        $pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,Q', 20, 150, 50, 50, $style, 'N');
        $pdf->Text(20, 145, 'QRCODE Q');

        // QRCODE,H : QR-CODE Best error correction
        $pdf->write2DBarcode('www.tcpdf.org', 'DATAMATRIX', 20, 210, 20, 20, $style, 'N');
        $pdf->Text(20, 205, 'DataMatrix');

        // ---------------------------------------------------------
        //Close and output PDF document
        $pdf->Output(__DIR__.'/../../../web/pdf/example_027.pdf', 'I');
        exit(0);
    }

    /**
     * @Route("/generate-sample-order/{document}/{format}", name="test_generate_sample_order_action")
     *
     * @return Response
     */
    public function generateOrderPdfAction($document = 'order_sample', $format = 'html')
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Amir Iskander');
        $pdf->SetTitle('Order Sample');
        $pdf->SetSubject('Order Sample');
        $pdf->SetKeywords('PDF, order, test, qr-code');
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();

        switch ($format) {
            case 'xml':
                $documentTemplate = __DIR__ . '/../../../app/Resources/views/default/document/' . $document .'.html.twig';
                $xmlContent = file_get_contents($documentTemplate);
                $templatePages = simplexml_load_string($xmlContent);
                $templatePages = $templatePages->page;
                foreach ($templatePages as $page) {
                    $pdf->AddPage();
                    $pdf->writeHTML($page, true, 0, true, 0);
                }
                break;
            case 'html':
            default:
                $html = $this->renderView('default/document/' . $document . '.html.twig', []);
                $pdf->writeHTML($html, true, 0, true, 0);
                break;
        }

        $pdf->lastPage();
        $pdf->Output(__DIR__.'/../../../web/pdf/order_sample.pdf', 'I');
        exit(0);
    }

    /**
     * @Route("/qr/read/image", name="test_read_qr_from_image_action")
     *
     * @return Response
     */
    public function readQrFromImageAction()
    {
        $initialTime = microtime(true);
        // $qrcode = new QrReader(__DIR__.'/../../public/qrcode.png');
        $qrcode = new QrReader(__DIR__.'/../../../web/pdf/microqr.png');
        $text = $qrcode->text(); //return decoded text from QR Code
        dump($text);

        dump((microtime(true) - $initialTime));

        die;
    }

    /**
     * @Route("/qr/read/pdf", name="test_read_qr_from_pdf_action")
     *
     * @return Response
     */
    public function readQrFromPdfAction()
    {
        $initialTime = microtime(true);

        $basePath  = __DIR__.'/../../../web/pdf/' . DIRECTORY_SEPARATOR;
        $pdfPath   = $basePath . 'scanned-document_smaller.pdf';
        $imagePath = $pdfPath . '_pages' . DIRECTORY_SEPARATOR;

        // Create directory to save PDF papers if it wasn't existed
        if (!file_exists($imagePath)) {
            mkdir($imagePath);
        }

        // Split PDF pages into compressed images
        $this->bashProcessWrapper->splitPdfPages($pdfPath, $imagePath . 'page.jpg');

        // Get all images that were splitted from the original PDF and sort them by name
        $generatedImages = scandir($imagePath);
        natsort($generatedImages);

        foreach ($generatedImages as $generatedImage) {
            if (!in_array($generatedImage, ['.', '..'])) {
                $qrcode = new QrReader($imagePath . DIRECTORY_SEPARATOR . $generatedImage);
                // $text = $qrcode->text(); //return decoded text from QR Code
                $qrcode->decode();
                $result = $qrcode->getResult();
                dump($result);
            }
        }

        dump((microtime(true) - $initialTime));
        die;
    }

    /**
     * @Route("/qr/zbar/read/image", name="test_zbar_read_qr_from_image_action")
     *
     * @return Response
     */
    public function zbarReadQrFromImageAction()
    {
        $initialTime = microtime(true);
        $imagePath = __DIR__.'/../../../web/pdf/5-scanned-documents.tiff';
        $decodedCodes = $this->bashProcessWrapper->readQrFromImage($imagePath);

        dump($decodedCodes);

        dump((microtime(true) - $initialTime));
        die;
    }
}
