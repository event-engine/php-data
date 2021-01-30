<?php
/**
 * This file is part of event-engine/php-data.
 * (c) 2018-2021 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace EventEngineTest\Data\Stub\ValueObject;

final class Version
{
    private $version;

    public static function fromInt(int $version): self
    {
        return new self($version);
    }

    private function __construct(int $version)
    {
        $this->version = $version;
    }

    public function toInt(): int
    {
        return $this->version;
    }

    /**
     * @param mixed $other
     */
    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->version === $other->version;
    }

    public function __toString(): string
    {
        return (string)$this->version;
    }
}
