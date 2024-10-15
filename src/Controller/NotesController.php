<?php

namespace App\Controller;

use App\Entity\Note;
use App\Filter\NoteFilter;
use App\Form\ChangeNote;
use App\Form\NoteFilterText;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Utils\Paginator;

#[Route('/notes')]
final class NotesController extends AbstractController
{
    #[Route(name: 'app_notes_index', methods: ['GET'])]
    public function index(Paginator $paginator, Request $request,NoteRepository $noteRepository): Response
    {
        $blogFilter = new NoteFilter();
        $form = $this->createForm(NoteFilterText::class, $blogFilter);
        $form->handleRequest($request);

        $sort = "";

        if (isset($_GET["ASC"])){
            $sort = "DESC";
        }
        elseif (isset($_GET["DESC"])) {
            $sort = "ASC";
        };

        $query = $noteRepository->findByNoteFilter($blogFilter, $sort);
        $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('notes/index.html.twig', [
            'notes' => $query->getQuery()->getResult(),
            'sort' => $sort ?: "ASC",
            'searchForm' => $form->createView(),
            'paginator' => $paginator,
        ]);

    }

    #[Route('/complete', name: 'app_notes_index_complete', methods: ['GET'])]
    public function complete(Paginator $paginator, Request $request,NoteRepository $noteRepository): Response
    {
        $blogFilter = new NoteFilter();
        $form = $this->createForm(NoteFilterText::class, $blogFilter);
        $form->handleRequest($request);

        $sort = "";

        if (isset($_GET["ASC"])){
            $sort = "DESC";
        }
        elseif (isset($_GET["DESC"])) {
            $sort = "ASC";
        };

        $query = $noteRepository->FindByNoteFilterComplite($blogFilter, true, $sort);
        $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('notes/index.html.twig', [
            'notes' => $query->getQuery()->getResult(),
            'sort' => $sort ?: "ASC",
            'searchForm' => $form->createView(),
            'paginator' => $paginator,
        ]);
    }

    #[Route('/active', name: 'app_notes_index_active', methods: ['GET'])]
    public function not_completed(Paginator $paginator, Request $request,NoteRepository $noteRepository): Response
    {
        $blogFilter = new NoteFilter();
        $form = $this->createForm(NoteFilterText::class, $blogFilter);
        $form->handleRequest($request);

        $sort = "";

        if (isset($_GET["ASC"])){
            $sort = "DESC";
        }
        elseif (isset($_GET["DESC"])) {
            $sort = "ASC";
        };

        $query = $noteRepository->FindByNoteFilterComplite($blogFilter, false, $sort);
        $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('notes/index.html.twig', [
            'notes' => $query->getQuery()->getResult(),
            'sort' => $sort ?: "ASC",
            'searchForm' => $form->createView(),
            'paginator' => $paginator,
        ]);

    }

    #[Route('/new', name: 'app_notes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('app_notes_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('notes/new.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notes_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->render('notes/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChangeNote::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_notes_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('notes/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notes_delete', methods: ['POST'])]
    public function delete(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_notes_index', [], Response::HTTP_SEE_OTHER);
    }
}
