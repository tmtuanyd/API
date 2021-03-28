<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordActionController;

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
 *              "denormalization_context"={"groups"={"put"}},
 *              "normalization_context"={"groups"={"get"}}
 *          },
 *           "put-reset-password"={
 *              "access_control"="object == user",
 *              "method"="PUT",
 *              "path"="/users/{id}/reset-password",
 *              "controller"=ResetPasswordActionController::class,
 *              "denormalization_context"={"groups"={"put-reset-password"}},
 *
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
    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = "ROLE_WRITER";
    const ROLE_EDITOR = "ROLE_EDITOR";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_SUPERADMIN = "ROLE_SUPERADMIN";
    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "got-comment", "get-blog-post-with-author"})
     * @Assert\NotBlank(groups={"post"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post")
     * @Assert\NotBlank(groups={"post"})
     */
    private $password;
    /**
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *      message="Passwords does not match",
     *     groups={"post"}
     * )
     */
    private $retypedPassword;

    /**
     * @Groups("put-reset-password")
     * @Assert\NotBlank()
     */
    private $newPassword;
    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getNewRetypedPassword()",
     *      message="Passwords does not match"
     * )
     */
    private $newRetypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank()
     * @UserPassword()
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put", "get", "post", "got-comment"})
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "put", "got-comment", "get-admin", "get-owner"})
     * @Assert\Email(groups={"post", "put"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=BlogPost::class, mappedBy="author")
     * @Groups({"get"})
     */
    private $blogPosts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    /**
     * @ORM\Column(type="json")
     * @Groups({"get-admin", "get-owner"})
     */
    private $roles = [];
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
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

    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }
    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
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

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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


    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }


    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }


    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }


    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }


    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }


    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }


    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

}
