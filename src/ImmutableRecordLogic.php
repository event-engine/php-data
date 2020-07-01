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

trait ImmutableRecordLogic
{
    /**
     * Override the method in the class using this trait to provide type hints for items of array properties
     *
     * @return array
     */
    private static function arrayPropItemTypeMap(): array
    {
        return [];
    }

    /**
     * Called in constructor after setting props but before not null assertion
     *
     * Override to set default props after construction
     */
    private function init(): void
    {
    }

    /**
     * @param array $recordData
     * @return self
     */
    public static function fromRecordData(array $recordData): self
    {
        return new self($recordData);
    }

    /**
     * @param array $nativeData
     * @return self
     */
    public static function fromArray(array $nativeData): self
    {
        return new self(null, $nativeData);
    }

    private function __construct(array $recordData = null, array $nativeData = null)
    {
        if (null === self::$__propTypeMap) {
            self::$__propTypeMap = self::buildPropTypeMap();
        }

        if ($recordData) {
            $this->setRecordData($recordData);
        }

        if ($nativeData) {
            $this->setNativeData($nativeData);
        }

        $this->init();

        $this->assertAllNotNull();
    }

    /**
     * @param array $recordData
     * @return self
     */
    public function with(array $recordData): self
    {
        $copy = clone $this;
        $copy->setRecordData($recordData);

        return $copy;
    }

    public function toArray(): array
    {
        $nativeData = [];
        $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

        foreach (self::$__propTypeMap as $key => [$type, $isNative, $isNullable]) {
            $specialKey = $key;

            if ($this instanceof SpecialKeySupport) {
                $specialKey = $this->convertKeyForArray($key);
            }

            switch ($type) {
                case ImmutableRecord::PHP_TYPE_STRING:
                case ImmutableRecord::PHP_TYPE_INT:
                case ImmutableRecord::PHP_TYPE_FLOAT:
                case ImmutableRecord::PHP_TYPE_BOOL:
                case ImmutableRecord::PHP_TYPE_ARRAY:
                    if (\array_key_exists($key, $arrayPropItemTypeMap) && ! self::isScalarType($arrayPropItemTypeMap[$key])) {
                        if ($isNullable && $this->{$key} === null) {
                            $nativeData[$specialKey] = null;
                            continue 2;
                        }

                        $nativeData[$specialKey] = \array_map(function ($item) use ($key, &$arrayPropItemTypeMap) {
                            return $this->voTypeToNative($item, $key, $arrayPropItemTypeMap[$key]);
                        }, $this->{$key});
                    } else {
                        $nativeData[$specialKey] = $this->{$key};
                    }
                    break;
                default:
                    if ($isNullable && (! isset($this->{$key}))) {
                        $nativeData[$specialKey] = null;
                        continue 2;
                    }
                    $nativeData[$specialKey] = $this->voTypeToNative($this->{$key}, $key, $type);
            }
        }

        return $nativeData;
    }

    public function equals(ImmutableRecord $other): bool
    {
        if (\get_class($this) !== \get_class($other)) {
            return false;
        }

        return $this->toArray() === $other->toArray();
    }

    private function setRecordData(array $recordData): void
    {
        foreach ($recordData as $key => $value) {
            $specialKey = $key;

            if ($this instanceof SpecialKeySupport) {
                $specialKey = $this->convertKeyForRecord($key);
            }

            $this->assertType($specialKey, $value);
            $this->{$specialKey} = $value;
        }
    }

    private function setNativeData(array $nativeData): void
    {
        $recordData = [];
        $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

        foreach ($nativeData as $key => $val) {
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

                $recordData[$key] = null;
                continue;
            }

            switch ($type) {
                case ImmutableRecord::PHP_TYPE_STRING:
                case ImmutableRecord::PHP_TYPE_INT:
                case ImmutableRecord::PHP_TYPE_FLOAT:
                case ImmutableRecord::PHP_TYPE_BOOL:
                    $recordData[$key] = $val;
                    break;
                case ImmutableRecord::PHP_TYPE_ARRAY:
                    if (\array_key_exists($key, $arrayPropItemTypeMap) && ! self::isScalarType($arrayPropItemTypeMap[$key])) {
                        $recordData[$key] = \array_map(function ($item) use ($key, &$arrayPropItemTypeMap) {
                            return $this->fromType($item, $arrayPropItemTypeMap[$key]);
                        }, $val);
                    } else {
                        $recordData[$key] = $val;
                    }
                    break;
                default:
                    $recordData[$key] = $this->fromType($val, $type);
            }
        }

        $this->setRecordData($recordData);
    }

    private function assertAllNotNull(): void
    {
        foreach (self::$__propTypeMap as $key => [$type, $isNative, $isNullable]) {
            if (! isset($this->{$key}) && ! $isNullable) {
                throw new \InvalidArgumentException(\sprintf(
                    'Missing record data for key %s of record %s.',
                    $key,
                    __CLASS__
                ));
            }
        }
    }

    private function assertType(string $key, $value): void
    {
        if (! isset(self::$__propTypeMap[$key])) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid property passed to Record %s. Got property with key ' . $key,
                __CLASS__
            ));
        }
        [$type, $isNative, $isNullable] = self::$__propTypeMap[$key];

        if (null === $value && $isNullable) {
            return;
        }

        if (! $this->isType($type, $key, $value)) {
            if ($type === ImmutableRecord::PHP_TYPE_ARRAY && \gettype($value) === ImmutableRecord::PHP_TYPE_ARRAY) {
                $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();
                throw new \InvalidArgumentException(\sprintf(
                    'Record %s data contains invalid value for property %s. Value should be an array of %s, but at least one item of the array has the wrong type.',
                    \get_called_class(),
                    $key,
                    $arrayPropItemTypeMap[$key]
                ));
            }

            throw new \InvalidArgumentException(\sprintf(
                'Record %s data contains invalid value for property %s. Expected type is %s. Got type %s.',
                \get_called_class(),
                $key,
                $type,
                (\is_object($value)
                    ? \get_class($value)
                    : \gettype($value))
            ));
        }
    }

    private function isType(string $type, string $key, $value): bool
    {
        switch ($type) {
            case ImmutableRecord::PHP_TYPE_STRING:
                return \is_string($value);
            case ImmutableRecord::PHP_TYPE_INT:
                return \is_int($value);
            case ImmutableRecord::PHP_TYPE_FLOAT:
                return \is_float($value) || \is_int($value);
            case ImmutableRecord::PHP_TYPE_BOOL:
                return \is_bool($value);
            case ImmutableRecord::PHP_TYPE_ARRAY:
                $isType = \is_array($value);

                if ($isType) {
                    $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

                    if (\array_key_exists($key, $arrayPropItemTypeMap)) {
                        foreach ($value as $item) {
                            if (! $this->isType($arrayPropItemTypeMap[$key], $key, $item)) {
                                return false;
                            }
                        }
                    }
                }

                return $isType;
            default:
                return $value instanceof $type;
        }
    }

    private static function buildPropTypeMap(): array
    {
        $refObj = new \ReflectionClass(__CLASS__);

        $props = $refObj->getProperties();

        $propTypeMap = [];

        foreach ($props as $prop) {
            if ($prop->getName() === '__propTypeMap' || $prop->getName() === '__schema' || $prop->getName() === '__arrayPropItemTypeMap') {
                continue;
            }

            if (! $refObj->hasMethod($prop->getName())) {
                throw new \RuntimeException(
                    \sprintf(
                        'No method found for Record property %s of %s that has the same name.',
                        $prop->getName(),
                        __CLASS__
                    )
                );
            }

            $method = $refObj->getMethod($prop->getName());

            if (! $method->hasReturnType()) {
                throw new \RuntimeException(
                    \sprintf(
                        'Method %s of Record %s does not have a return type',
                        $method->getName(),
                        __CLASS__
                    )
                );
            }

            /** @var \ReflectionNamedType $returnType */
            $returnType = $method->getReturnType();

            $type = $returnType->getName();

            $propTypeMap[$prop->getName()] = [$type, self::isScalarType($type), $method->getReturnType()->allowsNull()];
        }

        return $propTypeMap;
    }

    private static function isScalarType(string $type): bool
    {
        switch ($type) {
            case ImmutableRecord::PHP_TYPE_STRING:
            case ImmutableRecord::PHP_TYPE_INT:
            case ImmutableRecord::PHP_TYPE_FLOAT:
            case ImmutableRecord::PHP_TYPE_BOOL:
                return true;
            default:
                return false;
        }
    }

    private function fromType($value, string $type)
    {
        if (! \class_exists($type)) {
            throw new \InvalidArgumentException("Type class $type not found");
        }

        //Note: gettype() returns "integer" and "boolean" which does not match the type hints "int", "bool"
        switch (\gettype($value)) {
            case 'array':
                return $type::fromArray($value);
            case 'string':
                return $type::fromString($value);
            case 'integer':
                return \method_exists($type, 'fromInt')
                    ? $type::fromInt($value)
                    : $type::fromFloat($value);
            case 'float':
            case 'double':
                return $type::fromFloat($value);
            case 'boolean':
                return $type::fromBool($value);
            default:
                throw new \InvalidArgumentException("Cannot convert value to $type, because native type of value is not supported. Got " . \gettype($value));
        }
    }

    private function voTypeToNative($value, string $key, string $type)
    {
        if (\method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        if (\method_exists($value, 'toString')) {
            return $value->toString();
        }

        if (\method_exists($value, 'toInt')) {
            return $value->toInt();
        }

        if (\method_exists($value, 'toFloat')) {
            return $value->toFloat();
        }

        if (\method_exists($value, 'toBool')) {
            return $value->toBool();
        }

        throw new \InvalidArgumentException("Cannot convert property $key to its native counterpart. Missing to{nativeType}() method in the type class $type.");
    }

    private static function getArrayPropItemTypeMapFromMethodOrCache(): array
    {
        if (self::$__arrayPropItemTypeMap) {
            return self::$__arrayPropItemTypeMap;
        }

        return self::arrayPropItemTypeMap();
    }

    /**
     * @var array|null
     */
    private static $__propTypeMap;

    /**
     * @var array|null
     */
    private static $__arrayPropItemTypeMap;
}
