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
 * Implement this interface in your ImmutableRecord class if you have special values e.g. forgettable payload values.
 */
interface SensitiveImmutableRecord extends ImmutableRecord
{
    /**
     * Creates the record from sensitive data. Encrypted sensitive data has to be decrypted.
     *
     * @param array $sensitiveData
     * @param SensitiveValueConverter $sensitiveValueConverter
     * @param iterable|null $metadata Optional additional metadata e.g. message metadata or anything else
     * @return mixed
     */
    public static function fromSensitiveArray(
        array $sensitiveData,
        SensitiveValueConverter $sensitiveValueConverter,
        iterable $metadata = null
    );

    /**
     * Creates an array of the record with sensitive data. Sensitive decrypted data has to be encrypted.
     *
     * @param SensitiveValueConverter $sensitiveValueConverter
     * @param iterable|null $metadata Optional additional metadata e.g. message metadata or anything else
     * @return mixed
     */
    public function toSensitiveArray(SensitiveValueConverter $sensitiveValueConverter, iterable $metadata = null): array;

    /**
     * @param string $propertyName
     * @return bool
     */
    public static function isSensitiveProperty(string $propertyName): bool;
}
