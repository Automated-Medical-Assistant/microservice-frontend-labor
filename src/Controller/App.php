<?php declare(strict_types=1);


namespace App\Controller;

use App\Component\Ksb\Frontend\User\Communication\Form\ChangePasswordType;
use App\Form\StatusType;
use App\MessageHandler\NumberListHandler;
use App\Redis\RedisServiceInterface;
use MessageInfo\NumberAPIDataProvider;
use MessageInfo\NumberChangeStateRequestAPIDataProvider;
use MessageInfo\NumberListAPIDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class App extends AbstractController
{

    /**
     * @var \App\Redis\RedisServiceInterface
     */
    private RedisServiceInterface $redisService;

    private MessageBusInterface $messageBus;

    private NumberListHandler $numberListHandler;

    /**
     * @param \App\Redis\RedisServiceInterface $redisService
     * @param \Symfony\Component\Messenger\MessageBusInterface $messageBus
     * @param \App\MessageHandler\NumberListHandler $numberListHandler
     */
    public function __construct(\App\Redis\RedisServiceInterface $redisService, \Symfony\Component\Messenger\MessageBusInterface $messageBus, \App\MessageHandler\NumberListHandler $numberListHandler)
    {
        $this->redisService = $redisService;
        $this->messageBus = $messageBus;
        $this->numberListHandler = $numberListHandler;
    }


    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function home(): Response
    {
        $numbers = (array)$this->redisService->getAllStatus();
        $numberDtoList = [];
        foreach ($numbers as $number) {
            $numberDto = new NumberAPIDataProvider();
            $numberDto->fromArray(json_decode($number, true));
            $numberDtoList[] = $numberDto;
        }

        return $this->render('app/list.html.twig', [
            'numberDtoList' => $numberDtoList,
        ]);
    }

    /**
     * @Route("/status/{nummer}/update", name="status_update", methods={"GET","POST"})
     */
    public function updateResult(Request $request, string $nummer)
    {

        $nummer = json_decode((string)$this->redisService->get('number:' . $nummer), true);
        if (!isset($nummer['number'])) {
            throw new \Exception('Numer not found');
        }
        $numerAPI = new NumberAPIDataProvider();
        $numerAPI->fromArray($nummer);
        $status = $numerAPI->getStatus();

        $form = $this->createForm(StatusType::class, $numerAPI);
        $form->handleRequest($request);

        if ($status === null && $form->isSubmitted() && $form->isValid()) {
            $numerUpdateAPI = new NumberChangeStateRequestAPIDataProvider();
            $numerUpdateAPI->setStatus($numerAPI->getStatus());
            $numerUpdateAPI->setNumber($numerAPI->getNumber());

            $numerUpdateAPI->setModifiedStateDate((new \DateTime())->format('Y-m-d H:i:s'));

            $this->messageBus->dispatch($numerUpdateAPI);

            $numberListAPIDataProvider = new NumberListAPIDataProvider();
            $numberListAPIDataProvider->setNumbers($numerAPI);
            $this->numberListHandler->__invoke($numberListAPIDataProvider);
        }


        return $this->render('app/update.html.twig', [
            'numerApi' => $numerAPI,
            'form' => $form->createView(),
        ]);
    }
}
