<?php

namespace Alsbury\Malice\Component\Codeception;

use Alsbury\Malice\Component\Fixtures\FixtureConfig;

interface TestCaseInterface
{
    /**
     * @return FixtureConfig|null
     */
    public function _getFixtureConfig();
}