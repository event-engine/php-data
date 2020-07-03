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
use EventEngineTest\Data\Stub\ValueObject\Access;
use EventEngineTest\Data\Stub\ValueObject\ItemList;
use EventEngineTest\Data\Stub\ValueObject\Name;
use EventEngineTest\Data\Stub\ValueObject\Percentage;
use EventEngineTest\Data\Stub\ValueObject\Type;
use EventEngineTest\Data\Stub\ValueObject\Version;

final class TypeHintedImmutableRecordWithValueObjects implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private $name;

    private $type;

    private $version;

    private $itemList;

    private $access;

    private $percentage;

    private function init(): void
    {
        if (! isset($this->percentage)) {
            $this->percentage = Percentage::fromFloat(0.5);
        }
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return Type|null
     */
    public function type(): ?Type
    {
        return $this->type;
    }

    /**
     * @return Version
     */
    public function version(): Version
    {
        return $this->version;
    }

    /**
     * @return ItemList
     */
    public function itemList(): ItemList
    {
        return $this->itemList;
    }

    /**
     * @return Access
     */
    public function access(): Access
    {
        return $this->access;
    }

    /**
     * @return Percentage
     */
    public function percentage(): Percentage
    {
        return $this->percentage;
    }
}
