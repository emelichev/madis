<?php

namespace App\Domain\Registry\Model\ConformiteOrganisation;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Question
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $nom;

    /**
     * @var Processus|null
     */
    private $processus;

    /**
     * @var iterable
     */
    private $reponses;

    /**
     * @var int
     */
    private $position;

    /**
     * Question constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id       = Uuid::uuid4();
        $this->reponses = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getProcessus(): ?Processus
    {
        return $this->processus;
    }

    public function setProcessus(?Processus $processus): void
    {
        $this->processus = $processus;
    }

    public function getReponses(): iterable
    {
        return $this->reponses;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
