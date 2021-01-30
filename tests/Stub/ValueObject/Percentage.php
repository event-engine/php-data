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

final class Percentage
{
    private $percentage;

    public static function fromFloat(float $percentage): self
    {
        return new self($percentage);
    }

    private function __construct(float $percentage)
    {
        $this->percentage = $percentage;
    }

    public function toFloat(): float
    {
        return $this->percentage;
    }

    /**
     * @param mixed $other
     */
    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->percentage === $other->percentage;
    }

    public function __toString(): string
    {
        return (string)$this->percentage;
    }
}
