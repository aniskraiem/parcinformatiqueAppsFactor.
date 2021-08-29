<?php

namespace App\Entity;

use App\Repository\MaterielRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=MaterielRepository::class)
 */
class Materiel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")

     * @Assert\NotNull

     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private $modele;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\Length(min=10, max=20)
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private $add_mac;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private $add_ip;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")

     * @Assert\NotNull
     * @ORM\Column(type="date")
     */
    private $date_acc;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")

     * @ORM\Column(type="date")
     */
    private $date_fin;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une description")

     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private $Apps;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    public function getAddMac(): ?string
    {
        return $this->add_mac;
    }

    public function setAddMac(string $add_mac): self
    {
        $this->add_mac = $add_mac;

        return $this;
    }

    public function getAddIp(): ?string
    {
        return $this->add_ip;
    }

    public function setAddIp(string $add_ip): self
    {
        $this->add_ip = $add_ip;

        return $this;
    }

    public function getDateAcc(): ?\DateTimeInterface
    {
        return $this->date_acc;
    }

    public function setDateAcc(\DateTimeInterface $date_acc): self
    {
        $this->date_acc = $date_acc;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getApps(): ?string
    {
        return $this->Apps;
    }

    public function setApps(string $Apps): self
    {
        $this->Apps = $Apps;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }



}
