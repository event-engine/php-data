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
    public function it_throws_an_exception_if_no_type_hint_was_found()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf(
                'Could not find a type for property %s of record %s. ' .
                'No type hint or getter method with a return type is given.',
                'name',
                ImmutableRecordWithNoTypes::class
            )
        );

        ImmutableRecordWithNoTypes::fromArray($this->data);
    }
}
