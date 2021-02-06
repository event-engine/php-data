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

final class Access
{
    private $access;

    public static function fromBool(bool $access): self
    {
        return new self($access);
    }

    private function __construct(bool $access)
    {
        $this->access = $access;
    }

    public function toBool(): bool
    {
        return $this->access;
    }

    /**
     * @param mixed $other
     */
    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->access === $other->access;
    }

    public function __toString(): string
    {
        return $this->access ? 'TRUE' : 'FALSE';
    }
}
