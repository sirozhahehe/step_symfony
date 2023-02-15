<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Table('images')]
#[ORM\Entity()]
class Image
{

    private ?int $id = null;

    private string $path;

    private string $originalFilename;

    public function __construct(string $path, string $originalFilename)
    {
        $this->path = $path;
        $this->originalFilename = $originalFilename;
    }
}