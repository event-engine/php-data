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

final class ImmutableRecordWithTypedGetters implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private $name;
    private $type;
    private $version;
    private $itemList;
    private $access;
    private $percentage;

    private static function arrayPropItemTypeMap(): array
    {
        return ['itemList' => ImmutableItem::class];
    }

    private function init(): void
    {
        if(null === $this->percentage) {
            $this->percentage = 0.5;
        }
    }

    public function name() : string
    {
        return $this->name;
    }

    public function type() : ?string
    {
        return $this->type;
    }

    public function version() : int
    {
        return $this->version;
    }

    public function itemList(): array
    {
        return $this->itemList;
    }

    public function access(): bool
    {
        return $this->access;
    }

    public function percentage(): float
    {
        return $this->percentage;
    }
}
