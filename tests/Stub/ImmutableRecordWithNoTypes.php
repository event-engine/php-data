<?php

declare(strict_types=1);

namespace EventEngineTest\Data\Stub;

use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class ImmutableRecordWithNoTypes implements ImmutableRecord
{
    use ImmutableRecordLogic;

    private $name;
    private $type;
    private $version;

    public function name()
    {
        return $this->name;
    }

    public function type()
    {
        return $this->type;
    }

    public function version()
    {
        return $this->version;
    }
}
