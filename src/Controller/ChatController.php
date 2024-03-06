// src/Controller/ChatController.php
<?php


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use App\Repository\PharmacieRepository;

class ChatController extends AbstractController
{
    private $pharmacieRepository;

    public function __construct(PharmacieRepository $pharmacieRepository)
    {
        $this->pharmacieRepository = $pharmacieRepository;
    }

    #[Route('/chat', name: 'chat')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig');
    }

    #[Route('/chatbot', name: 'chatbot')]
    public function handleChatbotRequest(Request $request): Response
    {
        // Charger les pilotes BotMan
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
    
        // Créer une instance de BotMan
        $botman = BotManFactory::create([]);
    
        // Définir la logique de conversation du chatbot et les actions...
        $botman->hears('bonjour', function (BotMan $bot) {
            $bot->reply('Bonjour! Comment puis-je vous aider aujourd\'hui ?');
        });
    
        $botman->hears('trouver pharmacie près de {location}', function (BotMan $bot, $location) {
            // Récupérer les informations sur la pharmacie depuis la base de données
            $pharmacie = $this->pharmacieRepository->findOneByLocation($location);
            if ($pharmacie) {
                $reply = "La pharmacie la plus proche de {$location} est située à : ";
                $reply .= "Adresse: {$pharmacie->getAdresse()}, ";
                $reply .= "Email: {$pharmacie->getEmail()}, ";
                $reply .= "Téléphone: {$pharmacie->getPhoneNumber()}";
                $bot->reply($reply);
            } else {
                $bot->reply("Désolé, je n'ai pas pu trouver de pharmacie près de {$location}.");
            }
        });
    
        $botman->fallback(function (BotMan $bot) {
            $bot->reply('Désolé, je n\'ai pas compris. Pourriez-vous répéter, s\'il vous plaît ?');
        });
        
        // Commencer à écouter
        $botman->listen();
    
        // Renvoyer une réponse
        return new Response();
    }
}
