<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Annotation\ApiResource;
/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @Vich\Uploadable
 * @ApiResource(
 *
 * )
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="url")
     */
    private $file;

    /**
     * @ORM\Column(nullable=true)
     */
    private $url;

    public function getId()
    {
        return $this->id;
    }


    public function setId($id): void
    {
        $this->id = $id;
    }


    public function getFile()
    {
        return $this->file;
    }


    public function setFile($file): void
    {
        $this->file = $file;
    }


    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }

}
