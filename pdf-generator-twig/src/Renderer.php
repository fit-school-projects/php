<?php declare(strict_types=1);

namespace App;

use Dompdf\Dompdf;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer extends Dompdf
{
    private $twig;
    public function __construct()
    {
        parent::__construct();
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new Environment($loader);
    }
    public function makeHtml(Invoice $invoice): string
    {
        return $this->twig->render('invoice.twig', [
            'invoice' => $invoice,
        ]);
    }
    public function makePdf(Invoice $invoice): string
    {
        $this->loadHtml($this->makeHtml($invoice));
        $this->render();
        return $this->output();
    }
}
