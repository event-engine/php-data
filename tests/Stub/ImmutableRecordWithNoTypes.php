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

final class ImmutableRecordWithNoTypes implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private $name;
    private $type;
    private $version;
    private $itemList;
    private $access;
    private $percentage;

    public function name()
    {
        return $this->name;
    }

    public function type()
    {
        return $this->type;
    }

    public function version()
    {
        return $this->version;
    }

    public function itemList()
    {
        return $this->itemList;
    }

    public function access()
    {
        return $this->access;
    }

    public function percentage()
    {
        return $this->percentage;
    }
}
