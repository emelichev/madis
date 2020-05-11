<?php

namespace App\Domain\Registry\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteOrganisationParticipant
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
     * @var int
     */
    private $civilite;

    /**
     * @var string
     */
    private $fonction;

    /**
     * @var ConformiteOrganisationEvaluation
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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNomDeFamille(): string
    {
        return $this->nomDeFamille;
    }

    public function setNomDeFamille(string $nomDeFamille): void
    {
        $this->nomDeFamille = $nomDeFamille;
    }

    public function getCivilite(): int
    {
        return $this->civilite;
    }

    public function setCivilite(int $civilite): void
    {
        $this->civilite = $civilite;
    }

    public function getFonction(): string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): void
    {
        $this->fonction = $fonction;
    }

    public function getEvaluation(): ConformiteOrganisationEvaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(ConformiteOrganisationEvaluation $evaluation): void
    {
        $this->evaluation = $evaluation;
    }
}
