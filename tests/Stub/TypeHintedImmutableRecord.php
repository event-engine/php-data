<?php
/**
 * This file is part of event-engine/php-data.
 * (c) 2018-2020 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace EventEngineTest\Data\Stub;

use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class TypeHintedImmutableRecord implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private string $name;
    private ?string $type = null;
    private int $version;
    private array $itemList;
    private bool $access;
    private float $percentage;

    private static function arrayPropItemTypeMap(): array
    {
        return ['itemList' => ImmutableItem::class];
    }

    private function init(): void
    {
        if(!isset($this->percentage)) {
            $this->percentage = 0.5;
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function type(): ?string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function version(): int
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function itemList(): array
    {
        return $this->itemList;
    }

    /**
     * @return bool
     */
    public function access(): bool
    {
        return $this->access;
    }

    /**
     * @return float
     */
    public function percentage(): float
    {
        return $this->percentage;
    }
}
