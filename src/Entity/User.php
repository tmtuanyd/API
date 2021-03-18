<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"get"}},
 *     itemOperations={
 *          "get"={
 *             "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *              "normalization_context"={"groups"={"get"}}
 *          },
 *          "put"={
 *              "access_control"="object == user",
 *              "denormalization_context"={"groups"={"put"}}
 *          }
 *      },
 *     collectionOperations={
 *          "post"={
 *              "denormalization_context"={"groups"={"post"}}
 *          }
 *     },
 *
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email", "username"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post"})
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("put", "post")
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put", "get", "post"})
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "put"})
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=BlogPost::class, mappedBy="author")
     * @Groups({"get"})
     */
    private $blogPosts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="Author")
     * @Groups({"get"})
     */
    private $comments;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->removeElement($blogPost)) {
            // set the owning side to null (unless already changed)
            if ($blogPost->getAuthor() === $this) {
                $blogPost->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
