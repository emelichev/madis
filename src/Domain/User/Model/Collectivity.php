<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\User\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\AIPD\Model\ModeleAnalyse;
use App\Domain\Maturity\Model\Referentiel;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Embeddable\Address;
use App\Domain\User\Model\Embeddable\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Organisation.
 */
class Collectivity implements LoggableSubject
{
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $shortName;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $updatedBy;

    /**
     * @var int|null
     */
    private $siren;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string|null
     */
    private $website;

    /**
     * @var Address|null
     */
    private $address;

    /**
     * @var Contact|null
     */
    private $legalManager;

    /**
     * @var Contact|null
     */
    private $referent;

    /**
     * @var bool
     */
    private $differentDpo;

    /**
     * @var Contact|null
     */
    private $dpo;

    /**
     * @var bool
     */
    private $differentItManager;

    /**
     * @var Contact|null
     */
    private $itManager;

    /**
     * @var Collection
     */
    private $users;

    /**
     * @var Collection|Service[]
     */
    private $services;

    /**
     * @var bool|null
     */
    private $isServicesEnabled;

    /**
     * @var string|null
     */
    private $reportingBlockManagementCommitment;

    /**
     * @var string|null
     */
    private $reportingBlockContinuousImprovement;

    /**
     * @var Collection|Contact[]
     */
    private $comiteIlContacts;

    /**
     * @var Collection|ModeleAnalyse[]
     */
    private $modeleAnalyses;

    /**
     * @var Collection|Referentiel[]
     */
    private $referentiels;

    /**
     * @var bool
     */
    private $hasModuleConformiteTraitement;

    /**
     * @var bool
     */
    private $hasModuleConformiteOrganisation;
    /**
     * @var bool
     */
    private $hasModuleTools;

    /**
     * @var iterable
     */
    private $evaluations;

    /**
     * @var iterable
     */
    private $userReferents;

    /**
     * @var string|null
     */
    private $informationsComplementaires;

    /**
     * @var string|null
     */
    private $finessGeo;

    /**
     * @var int|null
     */
    private $population;
    /**
     * @var int|null
     */
    private $nbrAgents;
    /**
     * @var int|null
     */
    private $nbrCnil;

    /**
     * Collectivity constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                              = Uuid::uuid4();
        $this->users                           = new ArrayCollection();
        $this->comiteIlContacts                = new ArrayCollection();
        $this->services                        = new ArrayCollection();
        $this->active                          = true;
        $this->differentDpo                    = false;
        $this->differentItManager              = false;
        $this->hasModuleConformiteTraitement   = false;
        $this->hasModuleConformiteOrganisation = false;
        $this->hasModuleTools                  = false;
        $this->evaluations                     = [];
        $this->userReferents                   = [];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\mb_strlen($this->getName()) > 50) {
            return \mb_substr($this->getName(), 0, 50) . '...';
        }

        return $this->getName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getSiren(): ?int
    {
        return $this->siren;
    }

    public function setSiren(?int $siren): void
    {
        $this->siren = $siren;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
    }

    public function getLegalManager(): ?Contact
    {
        return $this->legalManager;
    }

    public function setLegalManager(?Contact $legalManager): void
    {
        $this->legalManager = $legalManager;
    }

    public function getReferent(): ?Contact
    {
        return $this->referent;
    }

    public function setReferent(?Contact $referent): void
    {
        $this->referent = $referent;
    }

    public function isDifferentDpo(): bool
    {
        return $this->differentDpo;
    }

    public function setDifferentDpo(bool $differentDpo): void
    {
        $this->differentDpo = $differentDpo;
    }

    public function getDpo(): ?Contact
    {
        return $this->dpo;
    }

    public function setDpo(?Contact $dpo): void
    {
        $this->dpo = $dpo;
    }

    public function isDifferentItManager(): bool
    {
        return $this->differentItManager;
    }

    public function setDifferentItManager(bool $differentItManager): void
    {
        $this->differentItManager = $differentItManager;
    }

    public function getItManager(): ?Contact
    {
        return $this->itManager;
    }

    public function setItManager(?Contact $itManager): void
    {
        $this->itManager = $itManager;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    public function addService(Service $service)
    {
        if ($this->services->contains($service)) {
            return;
        }

        $this->services[] = $service;
        $service->setCollectivity($this);
    }

    public function removeService(Service $service)
    {
        if (!$this->services->contains($service)) {
            return;
        }

        $this->services->removeElement($service);
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function setServices(Collection $services): void
    {
        $this->services = $services;
    }

    public function getIsServicesEnabled(): ?bool
    {
        return $this->isServicesEnabled;
    }

    public function setIsServicesEnabled(?bool $isServicesEnabled): void
    {
        $this->isServicesEnabled = $isServicesEnabled;
    }

    public function getReportingBlockManagementCommitment(): ?string
    {
        return $this->reportingBlockManagementCommitment;
    }

    public function setReportingBlockManagementCommitment(?string $reportingBlockManagementCommitment): void
    {
        $this->reportingBlockManagementCommitment = $reportingBlockManagementCommitment;
    }

    public function getReportingBlockContinuousImprovement(): ?string
    {
        return $this->reportingBlockContinuousImprovement;
    }

    public function setReportingBlockContinuousImprovement(?string $reportingBlockContinuousImprovement): void
    {
        $this->reportingBlockContinuousImprovement = $reportingBlockContinuousImprovement;
    }

    public function addComiteIlContact(ComiteIlContact $contact)
    {
        if ($this->comiteIlContacts->contains($contact)) {
            return;
        }

        $this->comiteIlContacts[] = $contact;
        $contact->setCollectivity($this);
    }

    public function removeComiteIlContact(ComiteIlContact $contact)
    {
        if (!$this->comiteIlContacts->contains($contact)) {
            return;
        }

        $this->comiteIlContacts->removeElement($contact);
    }

    /**
     * @return Collection|ComiteIlContact[]
     */
    public function getComiteIlContacts()
    {
        return $this->comiteIlContacts;
    }

    public function getModeleAnalyses()
    {
        return $this->modeleAnalyses;
    }

    public function setModeleAnalyses($modeleAnalyses): void
    {
        $this->modeleAnalyses = $modeleAnalyses;
    }

    public function addModeleAnalyse(ModeleAnalyse $modeleAnalyse)
    {
        if ($this->modeleAnalyses->contains($modeleAnalyse)) {
            return;
        }

        $this->modeleAnalyses[] = $modeleAnalyse;
        $modeleAnalyse->addAuthorizedCollectivity($this);
    }

    public function isHasModuleConformiteTraitement(): ?bool
    {
        return $this->hasModuleConformiteTraitement;
    }

    public function setHasModuleConformiteTraitement(bool $hasModuleConformiteTraitement): void
    {
        $this->hasModuleConformiteTraitement = $hasModuleConformiteTraitement;
    }

    public function isHasModuleConformiteOrganisation(): bool
    {
        return $this->hasModuleConformiteOrganisation;
    }

    public function setHasModuleConformiteOrganisation(bool $hasModuleConformiteOrganisation): void
    {
        $this->hasModuleConformiteOrganisation = $hasModuleConformiteOrganisation;
    }

    public function getEvaluations(): iterable
    {
        return $this->evaluations;
    }

    public function getUserReferents(): iterable
    {
        return $this->userReferents;
    }

    public function getInformationsComplementaires(): ?string
    {
        return $this->informationsComplementaires;
    }

    public function setInformationsComplementaires(?string $informationsComplementaires): void
    {
        $this->informationsComplementaires = $informationsComplementaires;
    }

    public function getFinessGeo(): ?string
    {
        return $this->finessGeo;
    }

    public function setFinessGeo(?string $finessGeo): void
    {
        $this->finessGeo = $finessGeo;
    }

    public function getReferentiels()
    {
        return $this->referentiels;
    }

    public function setReferentiels($referentiels): void
    {
        $this->referentiels = $referentiels;
    }

    public function addReferentiel(Referentiel $referentiel)
    {
        if ($this->referentiels->contains($referentiel)) {
            return;
        }

        $this->referentiels[] = $referentiel;
        $referentiel->addAuthorizedCollectivity($this);
    }

    public function getNbrAgents(): ?int
    {
        return $this->nbrAgents;
    }

    public function setNbrAgents(?int $nbrAgents): void
    {
        $this->nbrAgents = $nbrAgents;
    }

    public function getNbrCnil(): ?int
    {
        return $this->nbrCnil;
    }

    public function setNbrCnil(?int $nbrCnil): void
    {
        $this->nbrCnil = $nbrCnil;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): void
    {
        $this->population = $population;
    }

    public function isHasModuleTools(): bool
    {
        return $this->hasModuleTools;
    }

    public function setHasModuleTools(bool $hasModuleTools): void
    {
        $this->hasModuleTools = $hasModuleTools;
    }
}
