<?php

namespace App\Controller\Back;

use App\Entity\ProductCommand;
use App\Form\ProductCommandType;
use App\Repository\ProductCommandRepository;
use App\Service\TotalCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/product/command")
 */
class ProductCommandController extends AbstractController
{
    /**
     * @Route("/", name="back_product_command_index", methods={"GET"})
     */
    public function index(ProductCommandRepository $productCommandRepository): Response
    {
        return $this->render('back/product_command/index.html.twig', [
            'product_commands' => $productCommandRepository->findAll(),
        ]);
    }
  

    /**
     * @Route("/new", name="back_product_command_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, TotalCommand $totalCommand): Response
    {
        $productCommand = new ProductCommand();
        $form = $this->createForm(ProductCommandType::class, $productCommand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productCommand);
            $entityManager->flush();
            //Je veux appeller mon service de calcul de total de commande
            $totalCommand->updateTotalCommand($productCommand->getCommand()->getId());
            

            return $this->redirectToRoute('back_product_command_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product_command/new.html.twig', [
            'product_command' => $productCommand,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="back_product_command_show", methods={"GET"})
     */
    public function show(ProductCommand $productCommand): Response
    {
        return $this->render('back/product_command/show.html.twig', [
            'product_command' => $productCommand,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="back_product_command_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ProductCommand $productCommand, EntityManagerInterface $entityManager, TotalCommand $totalCommand): Response
    {
        $form = $this->createForm(ProductCommandType::class, $productCommand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            //Je veux appeller mon service de calcul de total de commande
            $totalCommand->updateTotalCommand($productCommand->getCommand()->getId());

            return $this->redirectToRoute('back_command_show', ["id"=>$productCommand->getCommand()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product_command/edit.html.twig', [
            'product_command' => $productCommand,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="back_product_command_delete", methods={"POST"})
     */
    public function delete(Request $request, ProductCommand $productCommand, EntityManagerInterface $entityManager, TotalCommand $totalCommand): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productCommand->getId(), $request->request->get('_token'))) {
            $entityManager->remove($productCommand);
            $entityManager->flush();
             //Je veux appeller mon service de calcul de total de commande
             $totalCommand->updateTotalCommand($productCommand->getCommand()->getId());
        }

        return $this->redirectToRoute('back_product_command_index', [], Response::HTTP_SEE_OTHER);
    }
}
