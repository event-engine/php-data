<?php
/**
 * This file is part of event-engine/php-data.
 * (c) 2018-2020 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EventEngineTest\Data;

use EventEngine\Data\SensitiveValueConverter;
use EventEngineTest\Data\Stub\InMemorySensitiveValueConverter;
use EventEngineTest\Data\Stub\Rot13SensitiveValueConverter;
use EventEngineTest\Data\Stub\SensitiveImmutableItem;
use EventEngineTest\Data\Stub\SensitiveRecord;
use EventEngineTest\Data\Stub\SensitiveTypeHintedImmutableRecord;
use EventEngineTest\Data\Stub\ValueObject\Percentage;
use EventEngineTest\Data\Stub\ValueObject\SensitiveItemList;
use PHPUnit\Framework\TestCase;

final class SensitiveImmutableRecordLogicTest extends TestCase
{
    /**
     * @test
     */
    public function it_detects_type_hinted_properties(): void
    {
        $rot13SensitiveValueConverter = new Rot13SensitiveValueConverter();

        $data = [
            'name' => 'John Smith',
            'version' => 1,
            'itemList' => [['name' => 'Awesome Tester']],
            'access' => true,
            'type' => null,
            'percentage' => 0.5,
        ];

        $typeHinted = SensitiveTypeHintedImmutableRecord::fromArray($data);
        $sensitiveData = $typeHinted->toSensitiveArray($rot13SensitiveValueConverter);

        $this->assertSame('Wbua Fzvgu', $sensitiveData['name']);
        $this->assertSame('Njrfbzr Grfgre', $sensitiveData['itemList'][0]['name']);

        $typeHinted = SensitiveTypeHintedImmutableRecord::fromSensitiveArray(
            $sensitiveData,
            $rot13SensitiveValueConverter
        );

        $this->assertSame('John Smith', $typeHinted->name());

        $this->assertEquals(
            $sensitiveData,
            $typeHinted->toSensitiveArray($rot13SensitiveValueConverter)
        );
        $this->assertEquals(
            $data,
            $typeHinted->toArray()
        );
    }

    /**
     * @test
     */
    public function it_supports_sensitive_array(): void
    {
        $rot13SensitiveValueConverter = new Rot13SensitiveValueConverter();

        // emulates encryption
        $sensitiveData = [
            SensitiveRecord::BANK_ACCOUNT_HOLDER => $rot13SensitiveValueConverter->convertValueForArray(
                SensitiveRecord::BANK_ACCOUNT_HOLDER,
                'John Smith',
                SensitiveRecord::class
            ),
            SensitiveRecord::SUCCESS_RATE => 33.33,
            SensitiveRecord::SENSITIVE_ITEM_LIST => [
                ['name' => $rot13SensitiveValueConverter->convertValueForArray('name', 'Awesome tester', SensitiveRecord::class)],
                ['name' => $rot13SensitiveValueConverter->convertValueForArray('name', 'John Smith', SensitiveRecord::class)],
            ],
        ];

        $this->assertSame('Wbua Fzvgu', $sensitiveData[SensitiveRecord::BANK_ACCOUNT_HOLDER]);
        $this->assertSame('Njrfbzr grfgre', $sensitiveData[SensitiveRecord::SENSITIVE_ITEM_LIST][0]['name']);
        $this->assertSame('Wbua Fzvgu', $sensitiveData[SensitiveRecord::SENSITIVE_ITEM_LIST][1]['name']);

        $data = [
            SensitiveRecord::BANK_ACCOUNT_HOLDER => 'John Smith',
            SensitiveRecord::SUCCESS_RATE => 33.33,
            SensitiveRecord::SENSITIVE_ITEM_LIST => [['name' => 'Awesome tester'], ['name' => 'John Smith']],
        ];
        // test with sensitive data
        $record = SensitiveRecord::fromSensitiveArray($sensitiveData, $rot13SensitiveValueConverter);

        $this->assertSame($sensitiveData, $record->toSensitiveArray($rot13SensitiveValueConverter));
        $this->assertSame($data, $record->toArray());
        $this->assertSame($data[SensitiveRecord::BANK_ACCOUNT_HOLDER], $record->bankAccountHolder());

        // test with normal data
        $record = SensitiveRecord::fromRecordData([
            SensitiveRecord::BANK_ACCOUNT_HOLDER => $data[SensitiveRecord::BANK_ACCOUNT_HOLDER],
            SensitiveRecord::SUCCESS_RATE => Percentage::fromFloat($data[SensitiveRecord::SUCCESS_RATE]),
            SensitiveRecord::SENSITIVE_ITEM_LIST => SensitiveItemList::fromArray($data[SensitiveRecord::SENSITIVE_ITEM_LIST]),
        ]);
        $this->assertSame($data, $record->toArray());
        $this->assertSame($sensitiveData, $record->toSensitiveArray($rot13SensitiveValueConverter));
    }

    /**
     * @test
     */
    public function it_supports_sensitive_array_with_deep_nesting(): void
    {
        $sensitiveValueConverter = new InMemorySensitiveValueConverter();

        $data = [
            SensitiveRecord::BANK_ACCOUNT_HOLDER => 'John Smith',
            SensitiveRecord::SUCCESS_RATE => 33.33,
            SensitiveRecord::SENSITIVE_ITEM_LIST => [['name' => 'Awesome tester'], ['name' => 'John Smith']],
        ];
        $record = SensitiveRecord::fromArray($data);

        $sensitiveData = $record->toSensitiveArray($sensitiveValueConverter);

        $storedSensitiveData = $sensitiveValueConverter->storedSensitiveData();

        $this->assertCount(3, $sensitiveValueConverter->storedSensitiveData());

        $this->assertArrayHasKey($sensitiveData[SensitiveRecord::BANK_ACCOUNT_HOLDER], $storedSensitiveData);
        $this->assertNotSame($data[SensitiveRecord::BANK_ACCOUNT_HOLDER], $sensitiveData[SensitiveRecord::BANK_ACCOUNT_HOLDER]);
        $this->assertNotSame($data[SensitiveRecord::SENSITIVE_ITEM_LIST][0]['name'], $sensitiveData[SensitiveRecord::SENSITIVE_ITEM_LIST][0]['name']);
        $this->assertNotSame($data[SensitiveRecord::SENSITIVE_ITEM_LIST][1]['name'], $sensitiveData[SensitiveRecord::SENSITIVE_ITEM_LIST][1]['name']);

        $this->assertSame($data, $record->toArray());
    }
}
