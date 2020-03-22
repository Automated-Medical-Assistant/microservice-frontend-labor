<?php declare(strict_types=1);


namespace App\Controller;

use App\Redis\RedisServiceInterface;
use MessageInfo\NumberAPIDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class App extends AbstractController
{

    /**
     * @var \App\Redis\RedisServiceInterface
     */
    private RedisServiceInterface $redisService;

    /**
     * @param \App\Redis\RedisServiceInterface $redisService
     */
    public function __construct(\App\Redis\RedisServiceInterface $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function home(): Response
    {
        return $this->render('app/home.html.twig', [
        ]);
    }


    /**
     * @Route("/status/{nummer}/update", name="status_update", methods={"GET","POST"})
     */
    public function updateResult(string $nummer)
    {
        $nummer = json_decode((string)$this->redisService->get('number:' . $nummer), true);
        if (!isset($nummer['number'])) {
            throw new \Exception('Numer not found');
        }
        $numerAPI = new NumberAPIDataProvider();
        $numerAPI->fromArray($nummer);
        dump($this->getUser());
        return $this->render('app/update.html.twig', [
            'numerApi' => $numerAPI
        ]);
    }
}
