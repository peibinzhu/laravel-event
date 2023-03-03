<?php

declare(strict_types=1);

namespace PeibinLaravel\Event\Annotations;

use Attribute;
use PeibinLaravel\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS)]
class Listener extends AbstractAnnotation
{
    public function __construct(public string $event, public int $priority = 0)
    {
    }
}
