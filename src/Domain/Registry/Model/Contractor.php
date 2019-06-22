<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\Embeddable\Address;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Contractor
{
    use CollectivityTrait;
    use CreatorTrait;
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
    private $referent;

    /**
     * @var bool
     */
    private $contractualClausesVerified;

    /**
     * @var bool
     */
    private $conform;

    /**
     * @var string|null
     */
    private $otherInformations;

    /**
     * @var Address|null
     */
    private $address;

    /**
     * @var Collection
     */
    private $treatments;

    /**
     * Contractor constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                         = Uuid::uuid4();
        $this->contractualClausesVerified = false;
        $this->conform                    = false;
        $this->treatments                 = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\strlen($this->getName()) > 50) {
            return \substr($this->getName(), 0, 50) . '...';
        }

        return $this->getName();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getReferent(): ?string
    {
        return $this->referent;
    }

    /**
     * @param string|null $referent
     */
    public function setReferent(?string $referent): void
    {
        $this->referent = $referent;
    }

    /**
     * @return bool
     */
    public function isContractualClausesVerified(): bool
    {
        return $this->contractualClausesVerified;
    }

    /**
     * @param bool $contractualClausesVerified
     */
    public function setContractualClausesVerified(bool $contractualClausesVerified): void
    {
        $this->contractualClausesVerified = $contractualClausesVerified;
    }

    /**
     * @return bool
     */
    public function isConform(): bool
    {
        return $this->conform;
    }

    /**
     * @param bool $conform
     */
    public function setConform(bool $conform): void
    {
        $this->conform = $conform;
    }

    /**
     * @return string|null
     */
    public function getOtherInformations(): ?string
    {
        return $this->otherInformations;
    }

    /**
     * @param string|null $otherInformations
     */
    public function setOtherInformations(?string $otherInformations): void
    {
        $this->otherInformations = $otherInformations;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    /**
     * @param Treatment $treatment
     */
    public function addTreatment(Treatment $treatment)
    {
        $this->treatments->add($treatment);
    }

    /**
     * @param Treatment $treatment
     */
    public function removeTreatment(Treatment $treatment)
    {
        $this->treatments->removeElement($treatment);
    }

    /**
     * @return ArrayCollection
     */
    public function getTreatments()
    {
        return $this->treatments;
    }
}
