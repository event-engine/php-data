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

use EventEngine\Data\SensitiveValueConverter;
use EventEngineTest\Data\Stub\SensitiveImmutableItem;

final class SensitiveItemList
{
    /**
     * @var SensitiveImmutableItem[]
     */
    private $items;

    public static function fromSensitiveArray(array $items, SensitiveValueConverter $sensitiveValueConverter, iterable $metadata = null): self
    {
        return new self(...array_map(function (array $item) use ($metadata, $sensitiveValueConverter) {
            return SensitiveImmutableItem::fromSensitiveArray($item, $sensitiveValueConverter, $metadata);
        }, $items));
    }

    public static function fromArray(array $items): self
    {
        return new self(...array_map(function (array $item) {
            return SensitiveImmutableItem::fromArray($item);
        }, $items));
    }

    public static function fromItems(SensitiveImmutableItem ...$items): self
    {
        return new self(...$items);
    }

    public static function emptyList(): self
    {
        return new self();
    }

    private function __construct(SensitiveImmutableItem ...$items)
    {
        $this->items = $items;
    }

    public function push(SensitiveImmutableItem $item): self
    {
        $copy = clone $this;
        $copy->items[] = $item;
        return $copy;
    }

    public function pop(): self
    {
        $copy = clone $this;
        \array_pop($copy->items);
        return $copy;
    }

    public function first(): ?SensitiveImmutableItem
    {
        return $this->items[0] ?? null;
    }

    public function last(): ?SensitiveImmutableItem
    {
        if (count($this->items) === 0) {
            return null;
        }

        return $this->items[count($this->items) - 1];
    }

    public function contains(SensitiveImmutableItem $item): bool
    {
        foreach ($this->items as $existingItem) {
            if ($existingItem->equals($item)) {
                return true;
            }
        }

        return false;
    }

    public function filter(callable $filter): self
    {
        $filteredItems = [];

        foreach ($this->items as $item) {
            if ($filter($item)) {
                $filteredItems[] = $item;
            }
        }

        $copy = clone $this;
        $copy->items = $filteredItems;
        return $copy;
    }

    /**
     * @return SensitiveImmutableItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return \array_map(function (SensitiveImmutableItem $item) {
            return $item->toArray();
        }, $this->items);
    }

    public function toSensitiveArray(SensitiveValueConverter $sensitiveValueConverter): array
    {
        return \array_map(function (SensitiveImmutableItem $item) use ($sensitiveValueConverter) {
            return $item->toSensitiveArray($sensitiveValueConverter);
        }, $this->items);
    }

    /**
     * @param mixed $other
     */
    public function equals($other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->toArray() === $other->toArray();
    }

    public function __toString(): string
    {
        return \json_encode($this->toArray());
    }
}
