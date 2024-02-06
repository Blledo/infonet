<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

final class CharacterController extends AbstractController
{
    #[Route('/', name: 'character_index', methods: ['GET'])]
    #[Route('/list', name: 'character_list', methods: ['GET'])]
    public function list(Request $request, CharacterRepository $characterRepository): Response
    {
        if ($request->query->has('search')) {
            $characters = $characterRepository->findBy(['name' => $request->query->get('search')]);
        } else {
            $characters = $characterRepository->findAll();
        }

        return $this->render('character/list.html.twig', [
            'characters' => $characters,
        ]);
    }

    #[Route('/character/{id<\d+>}', name: 'character_view', methods: ['GET', 'POST'])]
    #[Route('/character/{id<\d+>}/edit', name: 'character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picName = 'character_'.$character->getId();
            $file = $form['picture']->getData();
            $file->move('pictures', $picName);

            $character->setPicture('pictures/'.$picName);
            $entityManager->flush();

            return $this->redirectToRoute('character_index', ['id' => $character->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/character/{id}/delete', name: 'character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($character);
        $entityManager->flush();

        return $this->redirectToRoute('character_index', [], Response::HTTP_SEE_OTHER);
    }

}