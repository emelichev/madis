<?php

namespace App\Domain\User\Model;

use App\Application\Traits\Model\HistoryTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class EmailNotificationPreference
{
    use HistoryTrait;

    const NOTIF_TREATMENT               = 1;
    const NOTIF_SUBCONTRACTOR           = 2;
    const NOTIF_REQUEST                 = 4;
    const NOTIF_VIOLATION               = 8;
    const NOTIF_PROOF                   = 16;
    const NOTIF_PROTECT_ACTION          = 32;
    const NOTIF_MATURITY                = 64;
    const NOTIF_TREATMENT_CONFORMITY    = 128;
    const NOTIF_ORGANIZATION_CONFORMITY = 256;
    const NOTIF_AIPD                    = 512;
    const NOTIF_DOCUMENT                = 1024;

    const MODULES = [
        'treatment' => self::NOTIF_TREATMENT,
        'subcontractor' => self::NOTIF_SUBCONTRACTOR,
        'request' => self::NOTIF_REQUEST,
        'violation' => self::NOTIF_VIOLATION,
        'proof' => self::NOTIF_PROOF,
        'protect_action' => self::NOTIF_PROTECT_ACTION,
        'maturity' => self::NOTIF_MATURITY,
        'treatment_conformity' => self::NOTIF_TREATMENT_CONFORMITY,
        'organization_conformity' => self::NOTIF_ORGANIZATION_CONFORMITY,
        'aipd' => self::NOTIF_AIPD,
        'document' => self::NOTIF_DOCUMENT,
    ];

    /**
     * @var UuidInterface
     */
    private $id;

    private User $user;

    private string $frequency;

    private bool $enabled;

    private ?int $hour;

    private ?int $week;

    private ?int $day;

    private int $notificationMask;

    private $last_sent;

    public function __construct()
    {
        $this->id               = Uuid::uuid4();
        $this->notificationMask = 0;
        $this->enabled          = 1;
        $this->frequency   = 'none';
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): void
    {
        $this->frequency = $frequency;
    }

    /**
     * @return int|null
     */
    public function getHour(): ?int
    {
        return $this->hour;
    }

    /**
     * @param int|null $hour
     */
    public function setHour(?int $hour): void
    {
        $this->hour = $hour;
    }

    /**
     * @return int|null
     */
    public function getWeek(): ?int
    {
        return $this->week;
    }

    /**
     * @param int|null $week
     */
    public function setWeek(?int $week): void
    {
        $this->week = $week;
    }

    /**
     * @return int|null
     */
    public function getDay(): ?int
    {
        return $this->day;
    }

    /**
     * @param int|null $day
     */
    public function setDay(?int $day): void
    {
        $this->day = $day;
    }


    /**
     * @return mixed
     */
    public function getLastSent()
    {
        return $this->last_sent;
    }

    /**
     * @param mixed $last_sent
     */
    public function setLastSent($last_sent): void
    {
        $this->last_sent = $last_sent;
    }

    public function getNotificationMask(): int
    {
        return $this->notificationMask;
    }

    public function setNotificationMask(int $notificationMask): void
    {
        $this->notificationMask = $notificationMask;
    }

    /**
     * @return bool|int
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool|int $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }
}