<?php
// src/Service/QrCodeGenerator.php

namespace App\Service;

use App\Entity\Ordonnance;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeGenerator
{
    public function createQrCode(Ordonnance $ordonnance): ResultInterface
    {
        // Generate QR code content
        $qrCodeContent = "https://www.webmd.com/search?query=" .$ordonnance->getMedicaments();
        // You can add more information to the QR code content as needed

        // Generate the QR code with ordonnance data
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->writerOptions([])
            ->data($qrCodeContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->labelText('Scan pour voir les informactions de votre medicaments')
            ->labelFont(new NotoSans(20))
            ->validateResult(false)
            ->build();

        return $result;
    }
}
