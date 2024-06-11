<?php 
namespace App\Service;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generatePdf(string $template, array $data): Response
    {
        $options = new Options();
        $options->set(['defaultFont' => 'Arial',
                        'isHtml5ParserEnabled' => true,
                        'isPhpEnabled' => true,]);
        $dompdf = new Dompdf($options);
        // Render HTML using Twig
        $html = $this->twig->render($template, $data);
        $dompdf->loadHtml($html);
        // $dompdf->setPaper('A4', 'portrait');
        $dompdf->setPaper('A7', 'portrait');
        $dompdf->render();
        // Output PDF
        $output = $dompdf->output();
        return new Response($output, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }


    public function orderpdf(string $template2, array $data2): Response
    {
        $options2 = new Options();
        $options2->set(['defaultFont' => 'Arial',
                        'isHtml5ParserEnabled' => true,
                        'isPhpEnabled' => true,]);
        $dompdf2 = new Dompdf($options2);
        $html = $this->twig->render($template2, $data2);
        $dompdf2->loadHtml($html);
        $dompdf2->setPaper('A4', 'portrait');
        $dompdf2->render();
        // Output PDF
        $output = $dompdf2->output();
        return new Response($output, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
}
