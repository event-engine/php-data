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

use EventEngine\Data\ImmutableRecordDataConverter;
use EventEngineTest\Data\Stub\TypeHintedImmutableRecordWithValueObjects;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ImmutableRecordDataConverterTest extends TestCase
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
    public function it_converts_immutable_record_to_array()
    {
        $valueObjects = TypeHintedImmutableRecordWithValueObjects::fromArray($this->data);

        $dataConverter = new ImmutableRecordDataConverter();

        $this->data['type'] = null;
        $this->data['percentage'] = 0.5;


        $this->assertEquals(
            $this->data,
            $dataConverter->convertDataToArray(
                TypeHintedImmutableRecordWithValueObjects::class,
                $valueObjects
            )
        );
    }

    /**
     * @test
     */
    public function it_converts_stdClass_to_array()
    {
        $obj = new stdClass();

        $obj->test = "This is a test";
        $obj->msg = "With a message";

        $this->assertEquals([
                'test' => "This is a test",
                'msg' => "With a message",
            ],
            (new ImmutableRecordDataConverter())->convertDataToArray(stdClass::class, $obj)
        );
    }

    /**
     * @test
     */
    public function it_converts_array_to_immutable_record()
    {
        $dataConverter = new ImmutableRecordDataConverter();

        $this->assertTrue($dataConverter->canConvertTypeToData(TypeHintedImmutableRecordWithValueObjects::class));

        $valueObjects = $dataConverter->convertArrayToData(
            TypeHintedImmutableRecordWithValueObjects::class,
            $this->data
        );

        $this->data['type'] = null;
        $this->data['percentage'] = 0.5;

        $this->assertEquals(
            $this->data,
            $valueObjects->toArray()
        );
    }
}
