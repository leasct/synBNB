<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention la date d'arrivée doit être au bon format !")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être ultérieur à la date d'aujourd'hui")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention la date de départ doit être au bon format !")
     * @Assert\GreaterThan(propertyPath="startDate",message="La date de départ doit être plus éloignée que la date d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function isBookableDates(){
       /* $formatDay = function($datetime){
            return $datetime->format('Y-m-d');
        };*/

        // 1) il faut connaitre les dates qui sont impossibles
        $notAvailableDays = $this->ad->getNotAvailablesDays();

        // 2) il faut connaitre les dates choisies
        $bookingDays = $this->getDays();

        // 3) il faut comparer des dates en STRING
       $notAvailable = array_map(function($datetime){
                  return $datetime->format('Y-m-d');
                                              }, $notAvailableDays);

       $days = array_map(function($datetime){
                return $datetime->format('Y-m-d');
       },$bookingDays);

       // 4) comparaison:  si unedaychoisis est dans dateimpossible => peut pas reserver
       foreach($days as $day){

        if(array_search($day,$notAvailable) !==false ){
            return false;
        }
       }
       return true;
    }

    public function getDays(){

        $dateDebut = $this->startDate->getTimestamp();
        $dateFin = $this->endDate->getTimestamp();

        $resultat = range($dateDebut, $dateFin, 24 * 60 * 60);

        $days = array_map(
                        function($datetimestamp){
                            return new DateTime(date('Y-m-d',$datetimestamp));
                                 }, $resultat);

        return $days;
    }
     /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function getDuration(){
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
     }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prePersist(){

        if(empty($this->createdAt)){
             $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)){
              $this->amount = $this->getAd()->getPrice()  * $this->getDuration();
        }
    }
    
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
