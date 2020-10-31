<?php

namespace App\Entity;


use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

//HasLifecycle: cycle de vie == evenements sur une entité comme(PrePersist,PreUpdate)
//ORM == lien entre les données php et table sql , ici passwordconfirm n'as pa de lien avec bdd pas d'ORM
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"email"},
 * message="Un autre utilisateur s'est déjà inscrit avec cette adresse email, merci de la modifier.")
 */
class User implements UserInterface
{
        /**
        * Undocumented function
        *transformer les éléments dans le tableau => obtenir que les titres des roles
        *va boucler sur chaque objet role du tableau et les transformer en autre chose (propriété titre)
        *créer un nouveau tableau, dans chaque case y met les résultats de la fonction de pour chaque objet
         */
       public function getRoles()//permission
        {    
           // $roles =$this->userRoles->toArray(); //tableau d'objets de types roles
                
                $roles = $this->userRoles->map(function($role){
                    return $role->getTitle();
                })->toArray();//dans un simple tableau pas complexe
                
            $roles[]="ROLE_USER";
            return $roles;
        }
       
        public function getPassword(){//mdp hashé
            return $this->hash;
        }
        
        public function getSalt(){ } //pas besoin dajouter le grain de sel bcrypt le fait déjà

        public function getUsername(){//identifiant
            return $this->email;
        }

        public function eraseCredentials(){  }//pas de données sensibke

     /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *@Assert\EqualTo(propertyPath="hash", message="Vous n'avez pas correctement confirmé votre mot de passe.")
     */
    private $passwordConfirme;

    public function getPasswordConfirme(){
        return $this->passwordConfirme;
    }
    public function setPasswordConfirme($password){
        $this->passwordConfirme=$password;
    }
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     * message="Vous devez renseignez votre prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     * message="Vous devez renseignez votre nom de famille")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     * message="Veuillez renseigner un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(
     * message="Veuillez donnez une URL valide pour votre avatar")
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min=10, minMessage="Votre introduction doit faire au moins 10 caractères." )
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     * min=100, minMessage="Votre description doit faire au moins 100 caractères.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Ad::class, mappedBy="author")
     */
    private $ads;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="booker")
     */
    private $bookings;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $usersRoles;

    /**
     * Permet d'initliaser le slug
     * Cette fonction doit être appeller avant que l'on persiste notre objet et avant que l'on update notre entity
     *@ORM\PrePersist
     *@ORM\PreUpdate
     * @return void
     */
    public function initialiserSLug(){
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstName. ' '. $this->lastName);
        }
    }

    public function getFullName(){
        return $this->getFirstName()." ".$this->getLastName();
    }
    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->usersRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }
        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->contains($ad)) {
            $this->ads->removeElement($ad);
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setBooker($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getBooker() === $this) {
                $booking->setBooker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUsersRoles(): Collection
    {
        return $this->usersRoles;
    }

    public function addUsersRole(Role $usersRole): self
    {
        if (!$this->usersRoles->contains($usersRole)) {
            $this->usersRoles[] = $usersRole;
            $usersRole->addUser($this);
        }

        return $this;
    }

    public function removeUsersRole(Role $usersRole): self
    {
        if ($this->usersRoles->removeElement($usersRole)) {
            $usersRole->removeUser($this);
        }

        return $this;
    }
}