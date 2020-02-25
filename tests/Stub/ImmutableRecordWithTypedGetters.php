<?php

declare(strict_types=1);

namespace EventEngineTest\Data\Stub;

use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class ImmutableRecordWithTypedGetters implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private $name;
    private $type;
    private $version;

    public function name() : string
    {
        return $this->name;
    }

    public function type() : ?string
    {
        return $this->type;
    }

    public function version() : int
    {
        return $this->version;
    }
}
