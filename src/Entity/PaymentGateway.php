<?php

namespace App\Entity;

use App\Repository\PaymentGatewayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentGatewayRepository::class)]
class PaymentGateway
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $api_key = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $site_id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $secret_key = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $timestamp = null;

    public function __construct()
    {
        $this->timestamp = new \DateTime(); // Initialise à la date et l'heure actuelles
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiKey(string $api_key): self
    {
        $this->api_key = $api_key;
        return $this;
    }

    public function getSiteId(): ?string
    {
        return $this->site_id;
    }

    public function setSiteId(string $site_id): self
    {
        $this->site_id = $site_id;
        return $this;
    }

    public function getSecretKey(): ?string
    {
        return $this->secret_key;
    }

    public function setSecretKey(string $secret_key): self
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }


    public function setTimestamp(?\DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

}
