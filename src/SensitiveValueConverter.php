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

/**
 * Converts sensitive payload data for SensitiveImmutableRecord
 */
interface SensitiveValueConverter
{
    /**
     * Converts the given value to for the record e.g. decrypt value
     *
     * @param string $key Record key
     * @param mixed $value
     * @param string $recordType Class name of the record
     * @param iterable|null $metadata Additional metadata e.g. event metadata
     * @return mixed
     */
    public function convertValueForRecord(string $key, $value, string $recordType, ?iterable $metadata = null);

    /**
     * Converts the given value for the array data e.g. encrypt value
     *
     * @param string $key Record key
     * @param mixed $value
     * @param string $recordType Class name of the record
     * @param iterable|null $metadata Additional metadata e.g. event metadata
     * @return mixed
     */
    public function convertValueForArray(string $key, $value, string $recordType, ?iterable $metadata = null);
}
