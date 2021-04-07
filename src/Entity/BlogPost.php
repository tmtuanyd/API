<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *         "title": "partial",
 *         "content": "ipartial",
 *         "author": "exact",
 *         "author.name": "partial"
 *     }
 * )
 * @ApiFilter(
 *     DateFilter::class,
 *     properties={
 *          "published"
 *     }
 * )
 * @ApiFilter(RangeFilter::class, properties={"id"})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "published",
 *          "title"
 *     },
 *     arguments={"orderParameterName"="_order"}
 * )
 * @ApiResource(
 *     attributes={"order"={"id": "DESC"}, "pagination_items_per_page"=5},
 *     itemOperations={
 *     "get"={
 *      "normalization_context"={"groups"={"get-blog-post-with-author"}}
 *     },
 *     "put"={
 *          "access_control"="is_granted('ROLE_EDITOR') or is_granted('ROLE_WRITER') and object.getAuthor() == user"
 *     }
 *      },
 *     collectionOperations={
 *     "get",
 *     "post"={"access_control"="is_granted('ROLE_WRITER')"}
 *      },
 *     denormalizationContext={
 *          "groups"={"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
class BlogPost implements AuthoredEntityInterface, PublicDateEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     * @Groups({"post", "get-blog-post-with-author"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-blog-post-with-author"})
     */
    private $published;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min=20)
     * @Groups({"post"})
     */
    private $content;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post"})
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPosts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-post-with-author"})
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="blogPost")
     * @ApiSubresource()
     * @Groups({"get-blog-post-with-author"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Image::class)
     * @ORM\JoinTable()
     * @Groups("post", "get-blog-post-with-author")
     * @ApiSubresource()
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublicDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBlogPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlogPost() === $this) {
                $comment->setBlogPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }
    public function __toString():string
    {
        return $this->title;
    }
}
