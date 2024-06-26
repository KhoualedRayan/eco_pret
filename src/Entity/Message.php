<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2047)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_envoi = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $expediteur = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transaction $transaction = null;

    #[ORM\Column(nullable: true)]
    private ?bool $aEteLu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(\DateTimeInterface $date_envoi): static
    {
        $this->date_envoi = $date_envoi;

        return $this;
    }

    public function getExpediteur(): ?User
    {
        return $this->expediteur;
    }

    public function setExpediteur(?User $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }
    function getMessageDecrypter()
    {
        $cleSecrete = $_ENV['APP_CLE_CRYPTAGE'];
        $messageCrypte = base64_decode($this->contenu);
        $iv = substr($messageCrypte, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $messageCrypte = substr($messageCrypte, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($messageCrypte, 'aes-256-cbc', $cleSecrete, 0, $iv);
    }

    public function isAEteLu(): ?bool
    {
        return $this->aEteLu;
    }

    public function setAEteLu(?bool $aEteLu): static
    {
        $this->aEteLu = $aEteLu;

        return $this;
    }

}
