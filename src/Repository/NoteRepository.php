<?php

namespace App\Repository;

use App\Entity\Note;
use App\Filter\NoteFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\setMaxResults;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function FindByNoteFilter (NoteFilter $noteFilter,string $sort = ""){
        $searchNotes = $this->createQueryBuilder('find');
        if ($noteFilter->getText()){
            $searchNotes->where('find.text LIKE :text')->setParameter("text", '%' . $noteFilter->getText() . '%');
        }

        if ($sort != "") {
            $searchNotes->orderBy('find.datetime', '' .$sort . '');
        }

        if (isset($_GET["page"])){
            $searchNotes->setFirstResult(($_GET["page"] - 1) * 5);
        } else {
            $searchNotes->setFirstResult(0);
        }
        $searchNotes->setMaxResults(5);

        return $searchNotes;
    }

    public function FindByNoteFilterComplite (NoteFilter $noteFilter, string $complete , string $sort = ""){
        $searchNotes = $this->createQueryBuilder('find');
        if ($noteFilter->getText()){
            $searchNotes
                ->where('find.text LIKE :text')->setParameter("text", '%' . $noteFilter->getText() . '%')
                ->andWhere('find.complete = :complete')->setParameter("complete", '' . $complete . '');
        } else {
            $searchNotes->where('find.complete = :complete')->setParameter("complete", '' . $complete . '');
        }

        if ($sort != "") {
            $searchNotes->orderBy('find.datetime', '' .$sort . '');
        }

        if (isset($_GET["page"])){
            $searchNotes->setFirstResult(($_GET["page"] - 1) * 5);
        } else {
            $searchNotes->setFirstResult(0);
        }
        $searchNotes->setMaxResults(5);

        return $searchNotes;
    }
}
