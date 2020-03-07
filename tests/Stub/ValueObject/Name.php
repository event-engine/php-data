<?php
/**
 * This file is part of event-engine/php-data.
 * (c) 2018-2020 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace EventEngineTest\Data\Stub\ValueObject;

final class Name
{
    private $name;

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $other
     */
    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->name === $other->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
