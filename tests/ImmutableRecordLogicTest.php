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

use EventEngineTest\Data\Stub\ImmutableItem;
use EventEngineTest\Data\Stub\ImmutableRecordWithNoTypes;
use EventEngineTest\Data\Stub\ImmutableRecordWithTypedGetters;
use EventEngineTest\Data\Stub\RecordWithSpecialKey;
use EventEngineTest\Data\Stub\RecordWithStringList;
use EventEngineTest\Data\Stub\TypeHintedImmutableRecord;
use EventEngineTest\Data\Stub\TypeHintedImmutableRecordWithValueObjects;
use EventEngineTest\Data\Stub\ValueObject\Access;
use EventEngineTest\Data\Stub\ValueObject\ItemList;
use EventEngineTest\Data\Stub\ValueObject\Name;
use EventEngineTest\Data\Stub\ValueObject\Percentage;
use EventEngineTest\Data\Stub\ValueObject\Type;
use EventEngineTest\Data\Stub\ValueObject\Version;
use PHPUnit\Framework\TestCase;

final class ImmutableRecordLogicTest extends TestCase
{
    private $data = [];

    protected function setUp()
    {
        parent::setUp();

        $this->data = [
            'name' => 'test',
            'version' => 1,
            'itemList' => [['name' => 'one']],
            'access' => true,
        ];
    }

    /**
     * @test
     */
    public function it_detects_type_hinted_properties()
    {
        $typeHinted = TypeHintedImmutableRecord::fromArray($this->data);

        $this->data['type'] = null;
        $this->data['percentage'] = 0.5;

        $this->assertEquals(
            $this->data,
            $typeHinted->toArray()
        );
    }

    /**
     * @test
     */
    public function it_detects_coupled_getters_for_properties()
    {
        $typedGetters = ImmutableRecordWithTypedGetters::fromArray($this->data);

        $this->data['type'] = null;
        $this->data['percentage'] = 0.5;

        $this->assertEquals(
            $this->data,
            $typedGetters->toArray()
        );
    }

    /**
     * @test
     */
    public function it_can_handle_value_objects()
    {
        $valueObjects = TypeHintedImmutableRecordWithValueObjects::fromArray($this->data);

        $this->data['type'] = null;
        $this->data['percentage'] = 0.5;

        $this->assertEquals(
            $this->data,
            $valueObjects->toArray()
        );
    }

    /**
     * @test
     */
    public function it_takes_value_object_as_initialization_params()
    {
        $valueObjects = TypeHintedImmutableRecordWithValueObjects::fromRecordData([
            'name' => Name::fromString($this->data['name']),
            'type' => Type::fromString('value_object'),
            'version' => Version::fromInt($this->data['version']),
            'access' => Access::fromBool($this->data['access']),
            'percentage' => Percentage::fromFloat(0.9),
            'itemList' => ItemList::fromItems(ImmutableItem::fromRecordData(['name' => 'one'])),
        ]);

        $this->data['type'] = 'value_object';
        $this->data['percentage'] = 0.9;

        $this->assertEquals(
            $this->data,
            $valueObjects->toArray()
        );
    }

    /**
     * @test
     */
    public function it_returns_new_record_with_changed_properties()
    {
        $valueObjects = TypeHintedImmutableRecordWithValueObjects::fromArray($this->data);

        $changedValueObjects = $valueObjects->with([
            'version' => Version::fromInt(2),
            'percentage' => Percentage::fromFloat(0.9),
        ]);

        $this->data['type'] = null;
        $this->data['percentage'] = 0.5;

        $this->assertEquals(
            $this->data,
            $valueObjects->toArray()
        );

        $this->data['percentage'] = 0.9;
        $this->data['version'] = 2;

        $this->assertEquals(
            $this->data,
            $changedValueObjects->toArray()
        );
    }

    /**
     * @test
     */
    public function it_equals_other_record_with_same_values()
    {
        $valueObjects = TypeHintedImmutableRecordWithValueObjects::fromArray($this->data);
        $other = TypeHintedImmutableRecordWithValueObjects::fromArray($this->data);

        $this->assertTrue($valueObjects->equals($other));
    }

    /**
     * @test
     */
    public function it_supports_special_keys(): void
    {
        // emulates snake_case
        $recordArray = [
            RecordWithSpecialKey::BANK_ACCOUNT => '12324434',
            RecordWithSpecialKey::SUCCESS_RATE => 33.33,
            RecordWithSpecialKey::ITEM_LIST => [['name' => 'Awesome tester'], ['name' => 'John Smith']],
        ];
        $specialKey = RecordWithSpecialKey::fromArray($recordArray);
        $this->assertSame($recordArray, $specialKey->toArray());

        $specialKey = RecordWithSpecialKey::fromRecordData([
            RecordWithSpecialKey::BANK_ACCOUNT => $recordArray[RecordWithSpecialKey::BANK_ACCOUNT],
            RecordWithSpecialKey::SUCCESS_RATE => Percentage::fromFloat($recordArray[RecordWithSpecialKey::SUCCESS_RATE]),
            RecordWithSpecialKey::ITEM_LIST => ItemList::fromArray($recordArray[RecordWithSpecialKey::ITEM_LIST]),
        ]);
        $this->assertSame($recordArray, $specialKey->toArray());
    }

    /**
     * @test
     */
    public function it_throws_exception_if_unkown_property_provided()
    {
        $this->data['unknown'] = 'value';

        $this->expectExceptionMessage('Invalid property passed to Record ' . TypeHintedImmutableRecordWithValueObjects::class . '. Got property with key unknown');

        TypeHintedImmutableRecordWithValueObjects::fromArray($this->data);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_non_nullable_prop_is_missing()
    {
        unset($this->data['version']);

        $this->expectExceptionMessage('Missing record data for key version of record ' . TypeHintedImmutableRecord::class);

        TypeHintedImmutableRecord::fromArray($this->data);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_non_nullable_prop_should_be_set_to_null()
    {
        $this->data['version'] = null;

        $this->expectExceptionMessage('Got null for non nullable property version of Record ' . TypeHintedImmutableRecord::class);

        TypeHintedImmutableRecord::fromArray($this->data);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_property_value_has_wrong_type()
    {
        $this->data['version'] = 'v1';

        $this->expectExceptionMessage(\sprintf(
            'Record %s data contains invalid value for property version. Expected type is int. Got type string.',
            TypeHintedImmutableRecord::class
        ));

        TypeHintedImmutableRecord::fromArray($this->data);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_array_property_contains_invalid_value()
    {
        $stringList = ['abc', 123, 'def'];

        $this->expectExceptionMessage(\sprintf(
            'Record %s data contains invalid value for property stringList. Value should be an array of string, but at least one item of the array has the wrong type.',
            RecordWithStringList::class
        ));

        RecordWithStringList::fromArray(['stringList' => $stringList]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_no_type_hint_was_found()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf(
                'Method name of Record %s does not have a return type',
                ImmutableRecordWithNoTypes::class
            )
        );

        ImmutableRecordWithNoTypes::fromArray($this->data);
    }
}
