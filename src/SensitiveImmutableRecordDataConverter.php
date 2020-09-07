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
 * Data converter for SensitiveImmutableRecord instances
 */
final class SensitiveImmutableRecordDataConverter implements DataConverter
{
    /**
     * @var array
     */
    private $typeMap;

    /**
     * Is used for data conversion if it's not of type SensitiveImmutableRecord
     *
     * @var DataConverter
     */
    private $dataConverter;

    /**
     * @var SensitiveValueConverter
     */
    private $sensitiveValueConverter;

    /**
     * @param DataConverter $dataConverter For types other than SensitiveImmutableRecord
     * @param SensitiveValueConverter $sensitiveValueConverter
     * @param array $typeToClassMap
     */
    public function __construct(
        DataConverter $dataConverter,
        SensitiveValueConverter $sensitiveValueConverter,
        array $typeToClassMap = []
    ) {
        $this->typeMap = $typeToClassMap;
        $this->dataConverter = $dataConverter;
        $this->sensitiveValueConverter = $sensitiveValueConverter;
    }

    public function convertDataToArray(string $type, $data): array
    {
        if (\is_array($data)) {
            return $data;
        }

        if ($data instanceof SensitiveImmutableRecord || \is_callable([$data, 'toSensitiveArray'])) {
            return $data->toSensitiveArray($this->sensitiveValueConverter);
        }

        return $this->dataConverter->convertDataToArray($type, $data);
    }

    public function canConvertTypeToData(string $type): bool
    {
        $class = $this->getClassOfType($type);

        if (! \class_exists($class)) {
            return false;
        }

        if (true === \is_callable([$class, 'fromSensitiveArray'])) {
            return true;
        }

        return $this->dataConverter->canConvertTypeToData($type);
    }

    public function convertArrayToData(string $type, array $data)
    {
        $class = $this->getClassOfType($type);

        if (\in_array(SensitiveImmutableRecord::class, \class_implements($class), true)) {
            $class::fromSensitiveArray($data, $this->sensitiveValueConverter);
        }

        return $this->dataConverter->convertArrayToData($type, $data);
    }

    private function getClassOfType(string $type): string
    {
        return $this->typeMap[$type] ?? $type;
    }
}
