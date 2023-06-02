<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\OfferRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\TransactionRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractController
{
    #[Route('/market', name: 'app_market')]
    public function index(Request $request, OfferRepository $offerRepository , PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $offerRepository->getOfferPaginatorQuery(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render(
            'market/index.html.twig',
            [
                'pagination' => $pagination]
        );
    }
    #[Route('/market/team/{id}', name: 'teamMarketIndex')]
    public function teamMarketIndex(int $id,Request $request, OfferRepository $offerRepository , PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $offerRepository->getOfferPaginatorQueryWithId($id),
            $request->query->getInt('page', 1),
            12
            ,array('wrap-queries'=>true)
        );
        return $this->render(
            'market/index.html.twig',
            [
                'pagination' => $pagination,
                'team_id' => $id
            ]
        );
    }



    #[Route('/market/buy', name: 'buy_player')]
    public function buy(Request $request, OfferRepository $offerRepository , TeamRepository $teamRepository, TransactionRepository $transactionRepository,PlayerRepository $playerRepository): Response
    {
        $formData = $request->request->all();
        $team = $teamRepository->findTeamById($formData['team_id']);
        $offer = $offerRepository->findOfferById($formData['offer_id']);
        if($team != $offer->getPlayer()->getTeam())
        if($team->getCurrentBalance() >= $offer->getPrice()){
            $transaction = new Transaction();
            $transaction->setAmount($offer->getPrice()*-1);
            $transaction->setType("Buy Player -> ".$offer->getPlayer()->getName());
            $transaction->setTeam($team);
            $transaction->setCreatedAt(new DateTime());
            $transactionRepository->save($transaction, true);
            $transaction = new Transaction();
            $transaction->setAmount($offer->getPrice());
            $transaction->setType("Sell Player -> ".$offer->getPlayer()->getName());
            $transaction->setTeam($offer->getPlayer()->getTeam());
            $transaction->setCreatedAt(new DateTime());
            $transactionRepository->save($transaction, true);
            $player = $offer->getPlayer();
            $player->setTeam($team);
            $playerRepository->save($player,true);
            $offerRepository->remove($offer,true);
            $this->addFlash('success', 'Successfully Purchased player!');
        }
        else
            $this->addFlash('error', 'You Have No Balance!');

        return $this->redirectToRoute('team',['id'=>$formData['team_id']]);
    }
}
