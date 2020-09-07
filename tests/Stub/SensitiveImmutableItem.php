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

use EventEngine\Data\SensitiveImmutableRecord;
use EventEngine\Data\SensitiveImmutableRecordLogic;

final class SensitiveImmutableItem implements SensitiveImmutableRecord
{
    use SensitiveImmutableRecordLogic;

    private $name;

    public function name(): string
    {
        return $this->name;
    }

    public static function isSensitiveProperty(string $propertyName): bool
    {
        return $propertyName === 'name';
    }
}
