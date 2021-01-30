<?php
/**
 * This file is part of event-engine/php-data.
 * (c) 2018-2021 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EventEngine\Data;

/**
 * Implement this interface in your ImmutableRecord class if you have special keys like snake_case.
 */
interface SpecialKeySupport
{
    /**
     * Converts the given key to key name for the record. For example if the key is first_name and you have a class
     * property firstName then you have to convert it to camel case.
     *
     * @param string $key
     * @return string
     */
    public function convertKeyForRecord(string $key): string;

    /**
     * Converts the given key to key name for the array data. For example if you have a class property firstName
     * and want to have snake case array keys then you have to convert it to first_name.
     *
     * @param string $key
     * @return string
     */
    public function convertKeyForArray(string $key): string;
}
