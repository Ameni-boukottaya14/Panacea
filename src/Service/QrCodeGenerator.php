<?php

namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;
use App\Entity\User;

class QrCodeGenerator
{
    public function createQrCode(User $user): ResultInterface
    {
        // Generate vCard content
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "FN:" . $user->getNom() . " " . $user->getPrenom() . "\n";
        $vcard .= "EMAIL:" . $user->getEmail() . "\n";
        $vcard .= "TEL:" . $user->getTelephone() . "\n";
        // Add more fields as needed
        
        // End vCard
        $vcard .= "END:VCARD\n";

        // Generate the QR code with vCard data
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->writerOptions([])
            ->data($vcard)
            ->encoding(new Encoding('UTF-8'))
          //  ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->labelText('Scan to view contact information')
            ->labelFont(new NotoSans(20))
            ->validateResult(false)
            ->build();

        return $result;
    }
}