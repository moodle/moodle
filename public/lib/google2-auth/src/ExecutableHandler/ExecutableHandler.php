<?php
/**
 * Copyright 2024 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Auth\ExecutableHandler;

use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ExecutableHandler
{
    private const DEFAULT_EXECUTABLE_TIMEOUT_MILLIS = 30 * 1000;

    private int $timeoutMs;

    /** @var array<string|\Stringable> */
    private array $env = [];

    private ?string $output = null;

    /**
     * @param array<string|\Stringable> $env
     */
    public function __construct(
        array $env = [],
        int $timeoutMs = self::DEFAULT_EXECUTABLE_TIMEOUT_MILLIS,
    ) {
        if (!class_exists(Process::class)) {
            throw new RuntimeException(sprintf(
                'The "symfony/process" package is required to use %s.',
                self::class
            ));
        }
        $this->env = $env;
        $this->timeoutMs = $timeoutMs;
    }

    /**
     * @param string $command
     * @return int
     */
    public function __invoke(string $command): int
    {
        $process = Process::fromShellCommandline(
            $command,
            null,
            $this->env,
            null,
            ($this->timeoutMs / 1000)
        );

        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            throw new ExecutableResponseError(
                'The executable failed to finish within the timeout specified.',
                'TIMEOUT_EXCEEDED'
            );
        }

        $this->output = $process->getOutput() . $process->getErrorOutput();

        return $process->getExitCode();
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }
}
