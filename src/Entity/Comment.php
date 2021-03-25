<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={"get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_COMMENTATOR') or is_granted('ROLE_EDITOR') and object.getAuthor() == user"
 *           }
 *      },
 *     collectionOperations={
 *          "get",
 *          "post"={"access_control"="is_granted('ROLE_COMMENTATOR')"},
 *      },
 *      denormalizationContext={
 *          "groups"={"post"}
 *     },
 *     subresourceOperations={ "api_blog_posts_comments_get_subresource"={
 *              "method"="get",
 *              "normalization_context"={"groups"={"got-comment"}}
 *          }}
 * )
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment implements AuthoredEntityInterface, PublicDateEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"got-comment"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post", "got-comment", "get-blog-post-with-author"})
     * @Assert\Length(min=5, max=3000)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"got-comment"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"got-comment"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublicDateEntityInterface
    {
        $this->published = $published;

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

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }
}
