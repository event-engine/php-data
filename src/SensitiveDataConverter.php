<?php

/**
 * This file is part of event-engine/php-data.
 * (c) 2018-2020 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EventEngine\Data;

interface SensitiveDataConverter
{
    public function convertSensitiveDataToArray(string $type, $data): array;

    public function canConvertSensitiveTypeToData(string $type): bool;

    public function convertSensitiveArrayToData(string $type, array $data);
}
