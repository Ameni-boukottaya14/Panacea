<?php

namespace App\Controller;

require_once 'C:/Users/fares/OneDrive/Bureau/Project/Panacea/vendor/autoload.php';

use Twilio\Rest\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class SMSController extends AbstractController
    {
        #[Route('/SMS/{msg}', name: 'app_SMS')]
        public function index($msg): Response
        {
            // Your Account SID and Auth Token from twilio.com/console
            // To set up environmental variables, see http://twil.io/secure
            $account_sid = 'ACce2b51ef817e19bfe9172dfa1bbec086';
            $auth_token = 'b9b749b7e290385b3655eb57bf1ed3fe';
            // In production, these should be environment variables. E.g.:
            // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

            // A Twilio number you own with SMS capabilities
            $twilio_number = "+14154032649";

            $client = new Client($account_sid, $auth_token);

            $client->messages->create(
                // Where to send a text message (your cell phone?)
                '+21655545840',
                array(
                    'from' => $twilio_number,
                    'body' => $msg
                )
            );
            return new Response('SMS sent successfully');
        }

        #[Route('/SMShelp', name: 'app_SMS')]
        public function test(): Response
        {
                 $sid = 'ACce2b51ef817e19bfe9172dfa1bbec086';
                $token = 'b9b749b7e290385b3655eb57bf1ed3fe';
                $twilio = new Client($sid, $token);

                $validation_request = $twilio->validationRequests
                                            ->create("+14158675310", // phoneNumber
                                                    ["nour" => "+21627942274"]
                                            );

                print($validation_request->friendlyName);
            return new Response('SMS sent successfully');
        }

    }
