<?php


namespace App\Entity;


interface PublicDateEntityInterface
{
    public function setPublished(\DateTimeInterface $published): PublicDateEntityInterface;
}