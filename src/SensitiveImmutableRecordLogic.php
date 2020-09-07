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
 * @psalm-immutable
 */
trait SensitiveImmutableRecordLogic
{
    use ImmutableRecordLogic;

    private function __construct(
        array $recordData = null,
        array $nativeData = null,
        array $sensitiveData = null,
        SensitiveValueConverter $sensitiveValueConverter = null,
        iterable $metadata = null
    ) {
        if (null === self::$__propTypeMap) {
            self::$__propTypeMap = self::buildPropTypeMap();
        }

        if ($recordData) {
            $this->setRecordData($recordData);
        }

        if ($nativeData) {
            $this->setNativeData($nativeData);
        }

        if ($sensitiveData) {
            $recordData = [];
            $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

            foreach ($sensitiveData as $key => $val) {
                $recordData[$key] = $this->convertSensitiveValueForRecord(
                    $sensitiveValueConverter,
                    $arrayPropItemTypeMap,
                    $key,
                    $val,
                    $metadata
                );
            }
            $this->setRecordData($recordData);
        }

        $this->init();

        $this->assertAllNotNull();
    }

    public static function fromSensitiveArray(
        array $sensitiveData,
        SensitiveValueConverter $sensitiveValueConverter,
        iterable $metadata = null
    ): self {
        return new self(null, null, $sensitiveData, $sensitiveValueConverter, $metadata);
    }

    private function convertSensitiveValueForRecord(
        SensitiveValueConverter $sensitiveValueConverter,
        $arrayPropItemTypeMap,
        string $key,
        $val,
        ?iterable $metadata
    ) {
        $specialKey = $key;

        if ($this instanceof SpecialKeySupport) {
            $specialKey = $this->convertKeyForRecord($key);
        }

        if (! isset(self::$__propTypeMap[$specialKey])) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid property passed to Record %s. Got property with key ' . $specialKey,
                \get_called_class()
            ));
        }
        [$type, $isNative, $isNullable] = self::$__propTypeMap[$specialKey];

        if ($val === null) {
            if (! $isNullable) {
                throw new \RuntimeException("Got null for non nullable property $specialKey of Record " . \get_called_class());
            }

            return null;
        }

        $isSensitiveProperty = $this instanceof SensitiveImmutableRecord && true === $this->isSensitiveProperty($key);

        $recordType = get_class($this);

        switch ($type) {
            case ImmutableRecord::PHP_TYPE_STRING:
            case ImmutableRecord::PHP_TYPE_INT:
            case ImmutableRecord::PHP_TYPE_FLOAT:
            case ImmutableRecord::PHP_TYPE_BOOL:
                return $isSensitiveProperty ?
                    $sensitiveValueConverter->convertValueForRecord($specialKey, $val, $recordType)
                    : $this->convertValueForRecord($arrayPropItemTypeMap, $key, $val);
            case ImmutableRecord::PHP_TYPE_ARRAY:
                if (\array_key_exists($key, $arrayPropItemTypeMap)
                    && ! self::isScalarType($arrayPropItemTypeMap[$key])
                ) {
                    return \array_map(function ($item) use ($metadata, $sensitiveValueConverter, $specialKey, $key, &$arrayPropItemTypeMap) {
                        $isSensitiveProperty = $this instanceof SensitiveImmutableRecord && true === $this->isSensitiveProperty($key);

                        return $isSensitiveProperty
                            ? $this->fromSensitiveType(
                                $item,
                                $specialKey,
                                $arrayPropItemTypeMap[$key],
                                $sensitiveValueConverter,
                                $metadata
                            )
                            : $this->fromType($item, $arrayPropItemTypeMap[$key]);
                    }, $val);
                }

                return $isSensitiveProperty
                    ? $sensitiveValueConverter->convertValueForRecord($specialKey, $val, $recordType)
                    : $this->fromType($val, $type);
            default:
                return $isSensitiveProperty
                    ? $this->fromSensitiveType($val, $specialKey, $type, $sensitiveValueConverter, $metadata)
                    : $this->fromType($val, $type);
        }
    }

    private function fromSensitiveType(
        $value,
        string $key,
        string $type,
        SensitiveValueConverter $sensitiveValueConverter,
        ?iterable $metadata
    ) {
        if (! \class_exists($type)) {
            throw new \InvalidArgumentException("Type class $type not found");
        }

        $recordType = get_class($this);

        if (\in_array(SensitiveImmutableRecord::class, \class_implements($type), true)) {
            //Note: gettype() returns "integer" and "boolean" which does not match the type hints "int", "bool"
            switch (\gettype($value)) {
                case 'array':
                    return $type::fromSensitiveArray($value, $sensitiveValueConverter);
                case 'string':
                case 'integer':
                case 'float':
                case 'double':
                case 'boolean':
                    return $sensitiveValueConverter->convertValueForRecord($key, $value, $recordType, $metadata);
                default:
                    throw new \InvalidArgumentException(
                        "Cannot convert value to $type, because sensitive native type of value is not supported. Got " . \gettype($value)
                    );
            }
        } elseif (\method_exists($type, 'fromSensitiveArray')) {
            return $type::fromSensitiveArray($value, $sensitiveValueConverter);
        }

        return $this->fromType(
            $sensitiveValueConverter->convertValueForRecord($key, $value, $recordType, $metadata),
            $type
        );
    }

    public function toSensitiveArray(SensitiveValueConverter $sensitiveValueConverter, iterable $metadata = null): array
    {
        $nativeData = [];
        $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

        foreach (self::$__propTypeMap as $key => [$type, $isNative, $isNullable]) {
            $specialKey = $key;

            if ($this instanceof SpecialKeySupport) {
                $specialKey = $this->convertKeyForArray($key);
            }

            if (! $this instanceof SensitiveImmutableRecord
                || false === $this->isSensitiveProperty($key)
            ) {
                $nativeData[$specialKey] = $this->convertValueForArray($arrayPropItemTypeMap, $key, $type, $isNullable);
                continue;
            }

            $nativeData[$specialKey] = $this->convertSensitiveValueForArray(
                $sensitiveValueConverter,
                $arrayPropItemTypeMap,
                $key,
                $type,
                $isNullable,
                $metadata
            );
        }
        $recordType = get_class($this);

        return $nativeData;
    }

    private function convertSensitiveValueForArray(
        SensitiveValueConverter $sensitiveValueConverter,
        array $arrayPropItemTypeMap,
        string $key,
        string $type,
        bool $isNullable,
        ?iterable $metadata
    ) {
        $recordType = get_class($this);

        switch ($type) {
            case ImmutableRecord::PHP_TYPE_STRING:
            case ImmutableRecord::PHP_TYPE_INT:
            case ImmutableRecord::PHP_TYPE_FLOAT:
            case ImmutableRecord::PHP_TYPE_BOOL:
            case ImmutableRecord::PHP_TYPE_ARRAY:
                if (\array_key_exists($key, $arrayPropItemTypeMap)
                    && ! self::isScalarType($arrayPropItemTypeMap[$key])
                ) {
                    if ($isNullable && $this->{$key} === null) {
                        return $sensitiveValueConverter->convertValueForArray($key, null, $recordType, $metadata);
                    }

                    return \array_map(function ($item) use ($metadata, $sensitiveValueConverter, $key, &$arrayPropItemTypeMap) {
                        return $this->sensitiveVoTypeToNative(
                            $item,
                            $key,
                            $arrayPropItemTypeMap[$key],
                            $sensitiveValueConverter,
                            $metadata
                        );
                    }, $this->{$key});
                }

                return $sensitiveValueConverter->convertValueForArray($key, $this->{$key}, $recordType, $metadata);
            default:
                if ($isNullable && (! isset($this->{$key}))) {
                    return $sensitiveValueConverter->convertValueForArray($key, null, $recordType, $metadata);
                }

                return $this->sensitiveVoTypeToNative(
                    $this->{$key},
                    $key,
                    $type,
                    $sensitiveValueConverter,
                    $metadata
                );
        }
    }

    private function sensitiveVoTypeToNative(
        $value,
        string $key,
        string $type,
        SensitiveValueConverter $sensitiveValueConverter,
        ?iterable $metadata
    ) {
        if ($value instanceof SensitiveImmutableRecord
            || \method_exists($value, 'toSensitiveArray')
        ) {
            return $value->toSensitiveArray($sensitiveValueConverter);
        }
        $recordType = get_class($this);

        return $sensitiveValueConverter->convertValueForArray(
            $key,
            $this->voTypeToNative($value, $key, $type),
            $recordType,
            $metadata
        );
    }
}
