<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction/{id}', name: 'app_transaction')]
    public function index(int $id,Request $request, TransactionRepository $transactionRepository , PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $transactionRepository->getTransactionPaginatorQueryWithId($id),
            $request->query->getInt('page', 1),
            10
            ,array('wrap-queries'=>true)
        );

        if($pagination->count()==0)
            $this->addFlash('error', 'There Are No Transactions');

        return $this->render(
            'transaction/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }
}
