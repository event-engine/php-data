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

final class RecordWithStringList implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private $stringList;

    private static function arrayPropItemTypeMap(): array
    {
        return ['stringList' => ImmutableRecord::PHP_TYPE_STRING];
    }

    /**
     * @return array
     */
    public function stringList(): array
    {
        return $this->stringList;
    }
}
