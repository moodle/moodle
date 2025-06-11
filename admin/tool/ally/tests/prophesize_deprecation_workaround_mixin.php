<?php

namespace tool_ally;

use PHPUnit\Framework\Exception;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use ReflectionMethod;

trait prophesize_deprecation_workaround_mixin {
    /**
     * Workaround for prophesize() being deprecated in the version defined in Moodle's composer.json.
     * @throws ReflectionException
     */
    public function prophesize_without_deprecation_warning(?string $classOrInterface = null): ObjectProphecy {
        if (!class_exists(Prophet::class)) {
            throw new Exception('This test uses TestCase::prophesize(), but phpspec/prophecy is not installed. Please run "composer require --dev phpspec/prophecy".');
        }

        if (is_string($classOrInterface)) {
            $this->recordDoubledType($classOrInterface);
        }

        // Can't call $this->getProphet() or access $this->prophet due to private scope, call with reflection.
        $method = new ReflectionMethod($this, "getProphet");
        $method->setAccessible(true);

        /** @var Prophet $prophet */
        $prophet = $method->invoke($this);

        return $prophet->prophesize($classOrInterface);
    }
}
