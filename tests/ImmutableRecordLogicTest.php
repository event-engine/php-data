<?php

declare(strict_types=1);

namespace EventEngineTest\Data;

use EventEngineTest\Data\Stub\ImmutableRecordWithNoTypes;
use EventEngineTest\Data\Stub\ImmutableRecordWithTypedGetters;
use EventEngineTest\Data\Stub\TypeHintedImmutableRecord;
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
        ];
    }

    /**
     * @test
     */
    public function it_detects_type_hinted_properties()
    {
        $typeHinted = TypeHintedImmutableRecord::fromArray($this->data);

        $this->data['type'] = null;

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

        $this->assertEquals(
            $this->data,
            $typedGetters->toArray()
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
