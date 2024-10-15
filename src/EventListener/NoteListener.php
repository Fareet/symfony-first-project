<?php

namespace App\EventListener;;

use App\Entity\Note;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: Note::class)]
class NoteListener
{
//Событие удаления выполненых заметок, которые были выполнены более месяца назад

    public function postLoad(Note $note, PostLoadEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $oldNotes = $em->getRepository(Note::class)->findAll();

        $currentDate = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        foreach ($oldNotes as $oldNote) {

            if (
                ($oldNote->getDatetime()->diff($currentDate))->
                format("%m") > 0 &&
                $oldNote->isComplete() == true
            ){
                $em->remove($oldNote);
                $em->flush();
            }
        }
    }
}