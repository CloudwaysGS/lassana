<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Search;
use App\Form\ProduitType;
use App\Form\SearchType;
use App\Repository\ProduitRepository;
use App\Service\ProduitAddService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{


    #[Route('/produit/liste', name: 'produit_liste')]
    public function index(ProduitRepository $prod, Request $request, FlashyNotifier $notifier, PaginatorInterface $paginator): Response
    {
        $lastDayOfMonth = new \DateTime('last day of this month');
        $today = new \DateTime();
        $remainingDays = $lastDayOfMonth->diff($today)->days;
        $message = ($remainingDays === 2) ? "Attention : Il ne reste que 2 jours avant la fin du mois en cours !" : (($remainingDays === 1) ? "Attention : Il ne reste plus que 1 jour avant la fin du mois en cours !" : "");
        $produits = $prod->getLowStockProducts();

        foreach ($produits as $p){
            $this->addFlash('danger', "La quantité en stock ".$p->getLibelle()." est en baisse: ".$p->getQtStock());
        }

        $p = new Produit();
        $form = $this->createForm(ProduitType::class, $p, [
            'action' => $this->generateUrl('produit_add')
        ]);
        $search = new Search();
        $form2 = $this->createForm(SearchType::class, $search);
        $form2->handleRequest($request);
        $nom = $search->getNom();
        $pagination = $paginator->paginate(
            ($nom !== null && $nom !== '') ? $prod->findByName($nom) : $prod->findAllOrderedByDate(),
            $request->query->get('page', 1),
            10
        );
        $notifier->success('Bonjour '.$this->getUser()->getPrenom().' '.$this->getUser()->getNom().' je vous souhaite une excellente journée!!!');
        return $this->render('produit/liste.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
            'pagination' => $pagination,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'message' => $message
        ]);
    }

    #[Route('/produit/add', name: 'produit_add')]
    public function add(EntityManagerInterface $manager, ProduitAddService $produitAddService, Request $request): Response
    {
        $form = $this->createForm(ProduitType::class);
        $form->handleRequest($request);

        try {

            $produitAddService->handleFormSubmission($form);
            return $this->redirectToRoute('produit_liste');

        } catch (\Exception $e) {
            $this->addFlash('success', $e->getMessage());
            return $this->redirectToRoute('produit_liste');
        }
    }

    #[Route('/produit/delete/{id}', name: 'produit_delete')]
    public function delete(Produit $produit, ProduitRepository $repository){
        $repository->remove($produit,true);
        $this->addFlash('success', 'Le produit a été supprimé avec succès');
        return $this->redirectToRoute('produit_liste');
    }

    #[Route('/produit/edit/{id}', name: 'produit_edit')]
    public function edit($id,ProduitRepository $prod,Request $request,EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $lastDayOfMonth = new \DateTime('last day of this month');
        $today = new \DateTime();
        $remainingDays = $lastDayOfMonth->diff($today)->days;
        $message = ($remainingDays === 2) ? "Attention : Il ne reste que 2 jours avant la fin du mois en cours !" : (($remainingDays === 1) ? "Attention : Il ne reste plus que 1 jour avant la fin du mois en cours !" : "");

        $produits =$prod->find($id);
        $form = $this->createForm(ProduitType::class, $produits);
        $form->handleRequest($request);
        $search = new Search();
        $form2 = $this->createForm(SearchType::class, $search);
        $pagination = $paginator->paginate(
            $prod->findAllOrderedByDate(),
            $request->query->get('page', 1),
            10
        );
        if($form->isSubmitted() && $form->isValid()){
            $update = $form->getData()->getQtStock() * $form->getData()->getPrixUnit();
            if ($form->getData()->getNombre() !== 0){
                $updateDetail = $form->getData()->getNombre() * $form->getData()->getQtStock();
            }
            $form->getData()->setTotal($update);
            $form->getData()->setQtStockDetail($updateDetail);
            $entityManager->persist($form->getData());
           $entityManager->flush();
            return $this->redirectToRoute("produit_liste");
        }
        return $this->render('produit/liste.html.twig', [
            'pagination'=>$pagination,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'message' => $message
        ]);
    }


}
