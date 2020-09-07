<?php

declare(strict_types=1);

namespace EventEngineTest\Data\Stub;

use EventEngine\Data\SensitiveValueConverter;

final class InMemorySensitiveValueConverter implements SensitiveValueConverter
{
    private $storedSensitiveData = [];

    public function convertValueForRecord(string $key, $value, string $recordType, ?iterable $metadata = null)
    {
        if (!isset($this->storedSensitiveData[$value])) {
            throw new \RuntimeException(
                sprintf('Value "%s" for key "%s" not found in sensitive storage.', $value, $key)
            );
        }
        return $this->storedSensitiveData[$value];
    }

    public function convertValueForArray(string $key, $value, string $recordType, ?iterable $metadata = null)
    {
        $storedKey = $key . '_' . $recordType . bin2hex(random_bytes(6));
        $this->storedSensitiveData[$storedKey] = $value;

        return $storedKey;
    }

    /**
     * @return array
     */
    public function storedSensitiveData(): array
    {
        return $this->storedSensitiveData;
    }

}
