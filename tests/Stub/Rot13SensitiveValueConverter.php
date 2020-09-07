<?php

declare(strict_types=1);

namespace EventEngineTest\Data\Stub;

use EventEngine\Data\SensitiveValueConverter;

final class Rot13SensitiveValueConverter implements SensitiveValueConverter
{
    public function convertValueForRecord(string $key, $value, string $recordType, ?iterable $metadata = null)
    {
        return str_rot13($value);
    }

    public function convertValueForArray(string $key, $value, string $recordType, ?iterable $metadata = null)
    {
        return str_rot13($value);
    }
}
