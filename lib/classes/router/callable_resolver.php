<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core\router;

use Invoker\Exception\NotCallableException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\AdvancedCallableResolverInterface;

// phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Resolve middleware and route callables using PHP-DI.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class callable_resolver implements AdvancedCallableResolverInterface {
    /**
     * Create a new instance of the Callable Resolver.
     *
     * @param \Invoker\CallableResolver $callableresolver The DI Callable Resolver instance
     */
    public function __construct(
        /** @var \Invoker\CallableResolver The DI Callable Resolver instance */
        protected \Invoker\CallableResolver $callableresolver,
    ) {
    }

    #[\Override]
    public function resolve($toResolve): callable {
        return $this->callableresolver->resolve($this->translate_notation($toResolve));
    }

    #[\Override]
    public function resolveRoute($toResolve): callable {
        return $this->resolve_possible_signature($toResolve, 'handle', RequestHandlerInterface::class);
    }

    #[\Override]
    public function resolveMiddleware($toResolve): callable {
        return $this->resolve_possible_signature($toResolve, 'process', MiddlewareInterface::class);
    }

    /**
     * Translate Slim string callable notation ('nameOrKey:method') to PHP-DI notation ('nameOrKey::method').
     *
     * For a full list of supported callables, see the Slim Docs at
     * https://www.slimframework.com/docs/v4/objects/routing.html#container-resolution.
     *
     * @param mixed $toresolve
     * @return mixed
     */
    private function translate_notation(mixed $toresolve): mixed {
        if (is_string($toresolve) && preg_match(\Slim\CallableResolver::$callablePattern, $toresolve)) {
            $toresolve = str_replace(':', '::', $toresolve);
        }

        return $toresolve;
    }

    /**
     * Resolve a possible signature for a callable.
     *
     * @param mixed $toresolve The callable to resolve
     * @param string $method The method to resolve
     * @param string $typename The type name to resolve
     */
    private function resolve_possible_signature(
        mixed $toresolve,
        string $method,
        string $typename,
    ): callable {
        if (is_string($toresolve)) {
            $toresolve = $this->translate_notation($toresolve);

            try {
                $callable = $this->callableresolver->resolve([$toresolve, $method]);

                if (is_array($callable) && $callable[0] instanceof $typename) {
                    return $callable;
                }
            } catch (NotCallableException $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
                // Fall back to looking for a generic callable.
            }
        }

        return $this->callableresolver->resolve($toresolve);
    }
}
