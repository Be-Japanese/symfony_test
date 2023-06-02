<?php

namespace App\Controller;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Transaction;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\TransactionRepository;
use DateTime;
use Faker\Factory;
use Faker\Generator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create();
    }


    #[Route('/', name: 'app_team')]
    public function index(Request $request, TeamRepository $teamRepository ,  PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $teamRepository->getTeamPaginatorQuery(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render(
            'team/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }


    #[Route('/team/{id}', name: 'team', methods: ['GET', 'HEAD'])]
    public function show(int $id, TeamRepository $teamRepository ): Response
    {
        $team = $teamRepository->findTeamById($id);
        return $this->render(
            'team/team.html.twig',
            [
                'team' => $team
            ]
        );
    }


    public function edit(Request $request ,  TeamRepository $teamRepository ): RedirectResponse|Response
    {
        $formData = $request->query->all();
        $team = $teamRepository->findTeamById($formData['team_id']);
        $team->setName($formData['team_name']);
        $team->setCountry($formData['team_country']);
        $teamRepository->save($team,true);
        return $this->redirectToRoute(
            'team',
            [
                'id'=>$formData['team_id']
            ]
        );
    }


    public function delete(Request $request ,  TeamRepository $teamRepository ): RedirectResponse|Response
    {
        $formData = $request->query->all();
        $team = $teamRepository->findTeamById($formData['team_id']);
        $teamRepository->remove($team,true);
        return $this->redirectToRoute('app_team');
    }

    #[Route('/team/add', name: 'team_add', methods: ['POST'])]
    public function create( Request $request , TeamRepository $teamRepository , TransactionRepository $transactionRepository ,PlayerRepository $playerRepository): Response
    {
        $formData = $request->request->all();
        $team = new Team(
            $formData['team_name'],
                $formData['team_country']

        );
        $teamRepository->save($team,true);
        if(isset($formData['openingBalance']))
        {
            if(is_numeric($formData['openingBalance']))
            {
            $transaction = new Transaction();
            $transaction->setAmount($formData['openingBalance']);
            $transaction->setType("Opening Balance");
            $transaction->setTeam($team);
            $transaction->setCreatedAt(new DateTime());
            $transactionRepository->save($transaction, true);
            }
        }
        if(isset($formData['random_players']))
        {
            for ($i = 0; $i < 11; $i++)
            {
                $player = new Player(
                    $this->faker->firstName(),
                    $this->faker->lastName(),
                );
                $player->setTeam($team);
                $playerRepository->save($player,true);
            }
        }
        return $this->redirectToRoute('app_team');
    }


}
