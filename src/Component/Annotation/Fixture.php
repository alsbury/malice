<?php

namespace Alsbury\Malice\Component\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 *@Annotation
 */
final class Fixture extends Annotation
{
    public $bundle;
    public $fixture;
}
