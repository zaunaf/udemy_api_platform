<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * @ApiResource(
 *      itemOperations={
 *          "get"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *              "normalization_context"={
 *                   "groups"={"get"}
 *               }
 *           },
 *          "put"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "denormalization_context"={
 *                   "groups"={"put"}
 *               }
 *           },
 *      },
 *      collectionOperations={
 *          "post"={
 *               "denormalization_context"={
 *                   "groups"={"post"}
 *               }
 *           }
 *      }
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-comment-with-author", "get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)     
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     * @Groups({"get", "post", "get-comment-with-author", "get-blog-post-with-author"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)     
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern="/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}/",
     *  message="Password must have at least 8 characters and have at least one number, one lower case letter and one uppercase letter"
     * )
     * @Groups({"put", "post"})
     */
    private $password;

    /**     
     * @Assert\NotBlank()
     * @Assert\Expression(
     *  "this.getPassword() === this.getRetypedPassword()",
     *   message="Passwords does not match"
     * )
     * @Groups({"put", "post"})
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)     
     * @Assert\NotBlank()
     * @Groups({"get", "post", "put", "get-comment-with-author", "get-blog-post-with-author"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)     
     * @Assert\Email()
     * @Groups({"get", "post", "put", "get-comment-with-author", "get-blog-post-with-author"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="\App\Entity\BlogPost", mappedBy="author")
     * @Groups({"get"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="\App\Entity\Comment", mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    public function __construct()
    {   
        $this->posts = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return self
     */
    public function setPosts($posts): self
    {
        $this->posts = $posts;
        return $this;
    }
    
    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Setter for Comments
     * @var [type] comments
     *
     * @return self
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword)
    {
        $this->retypedPassword = $retypedPassword;
        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials() 
    {
        
    }

    
}
