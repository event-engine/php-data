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

use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;
use EventEngine\Data\SpecialKeySupport;
use EventEngineTest\Data\Stub\ValueObject\ItemList;
use EventEngineTest\Data\Stub\ValueObject\Percentage;

final class RecordWithSpecialKey implements ImmutableRecord, SpecialKeySupport
{
    use ImmutableRecordLogic;

    public const BANK_ACCOUNT = 'bank_account';
    public const SUCCESS_RATE = 'success_rate';
    public const ITEM_LIST = 'item_list';

    /**
     * @var string
     */
    private $bankAccount;

    /**
     * @var Percentage
     */
    private $successRate;

    /**
     * @var ItemList
     */
    private $itemList;

    /**
     * @return mixed
     */
    public function bankAccount(): string
    {
        return $this->bankAccount;
    }

    /**
     * @return Percentage
     */
    public function successRate(): Percentage
    {
        return $this->successRate;
    }

    /**
     * @return ItemList
     */
    public function itemList(): ItemList
    {
        return $this->itemList;
    }

    public function convertKeyForRecord(string $key): string
    {
        switch ($key) {
            case self::SUCCESS_RATE:
                return 'successRate';
            case self::ITEM_LIST:
                return 'itemList';
            default:
                return 'bankAccount';
        }
    }

    public function convertKeyForArray(string $key): string
    {
        switch ($key) {
            case 'successRate':
                return self::SUCCESS_RATE;
            case 'itemList':
                return self::ITEM_LIST;
            default:
                return self::BANK_ACCOUNT;
        }
    }
}
