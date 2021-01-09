<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
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
     * @Assert\Date(message="La date d'arrivée doit être au bon format")
     * @Assert\GreaterThan("today",message="La date doit être ultérieur à celle d'aujourd'hui.")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="La date de départ doit être au bon format")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être plus eloigné que la date d'arrivée.")
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

    /**
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist(){
        if(empty($this -> createdAt)){
            $this -> createdAt = new \DateTime();
        }

        if(empty($this -> amount)){
            $this -> amount = $this -> getDuration() * $this -> ad -> getPrice();
        }
        
    }

    public function getDuration(){
        $duration = $this -> endDate -> diff($this -> startDate);
        return $duration -> days;
    }

    public function isBookableDates(){

        $notAvailableDays = $this -> ad -> getNotAvailableDays();
        $bookingDays = $this -> getDays(); 

        $formatDays = function($stringDays){
            return $stringDays -> format('Y-m-d');
        };

        $days = array_map($formatDays,$bookingDays);

        $days2 = array_map($formatDays,$notAvailableDays); 

        foreach($days as $day){
            if(array_search($day , $days2) !== false) return false ;
        }
        return true ;
    }

    public function getDays(){

        $result = range(
            $this -> startDate -> getTimeStamp(),
            $this -> endDate -> getTimeStamp(),
            24 * 60 * 60
        );

        $days = array_map(function($daysTimeStamp){
            return new \DateTime(date('Y-m-d', $daysTimeStamp));
        }, $result);

        return $days;
    }
}
