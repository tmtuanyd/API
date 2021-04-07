<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UploadImageActionController;
/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *    attributes={"order"={"id": "ASC"}},
 *    collectionOperations={
 *     "get",
 *     "post"={
 *          "method"="POST",
 *          "path"="/images",
 *          "controller"=UploadImageActionController::class,
 *          "deserialize"=false,
 *     }
 *     }
 * )
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="url")
     * @var string|null
     */
    private $file;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"get-blog-post-with-author"})
     */
    private $url;

    public function getId()
    {
        return $this->id;
    }


    public function getFile()
    {
        return $this->file;
    }


    public function setFile(?File $file=null): void
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
    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->id . ':' . $this->url;
    }

}
