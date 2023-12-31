<?php

namespace App\Domain\Registry\Model\ConformiteOrganisation;

use App\Domain\Reporting\Model\LoggableSubject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Participant implements LoggableSubject
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $prenom;

    /**
     * @var string
     */
    private $nomDeFamille;

    /**
     * @var string
     */
    private $civilite;

    /**
     * @var string
     */
    private $fonction;

    /**
     * @var Evaluation
     */
    private $evaluation;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNomDeFamille(): ?string
    {
        return $this->nomDeFamille;
    }

    public function setNomDeFamille(string $nomDeFamille): void
    {
        $this->nomDeFamille = $nomDeFamille;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): void
    {
        $this->civilite = $civilite;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): void
    {
        $this->fonction = $fonction;
    }

    public function getEvaluation(): Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(Evaluation $evaluation): void
    {
        $this->evaluation = $evaluation;
    }

    public function __clone()
    {
        $this->id = Uuid::uuid4();
    }

    public function __toString(): string
    {
        return $this->nomDeFamille . ' ' . $this->prenom;
    }
}
