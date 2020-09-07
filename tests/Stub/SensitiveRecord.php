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
use EventEngineTest\Data\Stub\ValueObject\Percentage;
use EventEngineTest\Data\Stub\ValueObject\SensitiveItemList;

final class SensitiveRecord implements SensitiveImmutableRecord
{
    use SensitiveImmutableRecordLogic;

    public const BANK_ACCOUNT_HOLDER = 'bankAccountHolder';
    public const SUCCESS_RATE = 'successRate';
    public const SENSITIVE_ITEM_LIST = 'sensitiveItemList';

    /**
     * @var string
     */
    private $bankAccountHolder;

    /**
     * @var Percentage
     */
    private $successRate;

    /**
     * @var SensitiveItemList
     */
    private $sensitiveItemList;

    /**
     * @return mixed
     */
    public function bankAccountHolder(): string
    {
        return $this->bankAccountHolder;
    }

    /**
     * @return Percentage
     */
    public function successRate(): Percentage
    {
        return $this->successRate;
    }

    /**
     * @return SensitiveItemList
     */
    public function sensitiveItemList(): SensitiveItemList
    {
        return $this->sensitiveItemList;
    }

    public static function isSensitiveProperty(string $propertyName): bool
    {
        return $propertyName === self::BANK_ACCOUNT_HOLDER || $propertyName === self::SENSITIVE_ITEM_LIST ;
    }
}
