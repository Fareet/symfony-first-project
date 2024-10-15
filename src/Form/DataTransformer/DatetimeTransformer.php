<?php

namespace App\Form\DataTransformer;

use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DatetimeTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,)
    {
    }
    public function transform($datetime): string
    {
        if (null === $datetime) {
            return '';
        }
        return date_format($datetime, 'Y-m-d H:i:s');
    }
    public function reverseTransform($datetime): ?\DateTime
    {
        $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

        return $datetime;
    }
}