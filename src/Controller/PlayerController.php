<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use App\Repository\PlayerRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player')]
    public function index(): Response
    {
        return $this->render(
            'player/index.html.twig',
            [
            'controller_name' => 'PlayerController',
            ]
        );
    }


    public function edit(Request $request ,  PlayerRepository $playerRepository ): RedirectResponse|Response
    {
        $formData = $request->query->all();
        $player = $playerRepository->findPlayerById($formData['player_id']);
        $player->setName($formData['player_name']);
        $playerRepository->save($player,true);
        return $this->redirectToRoute('team',['id'=>$player->getTeam()->getId()]);
    }

    public function sell(Request $request ,  PlayerRepository $playerRepository ,  OfferRepository $offerRepository ): RedirectResponse|Response
    {
        $formData = $request->query->all();
        $player = $playerRepository->findPlayerById($formData['player_id']);
        $offer = new Offer();
        $offer->setPrice($formData['player_price']);
        $offer->setPlayer($player);
        $offer->setCreatedAt(  new DateTime());
        $offerRepository->save($offer,true);
        return $this->redirectToRoute('app_market');
    }

    public function delete(Request $request ,  PlayerRepository $playerRepository ): RedirectResponse|Response
    {
        $formData = $request->query->all();
        $player = $playerRepository->findPlayerById($formData['player_id']);
        $playerRepository->remove($player,true);
        return $this->redirectToRoute(
            'team',
            [
                'id'=>$player->getTeam()->getId()
            ]
        );

    }
}
