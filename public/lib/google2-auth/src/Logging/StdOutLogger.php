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

namespace Google\Auth\Logging;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Stringable;

/**
 * A basic logger class to log into stdOut for GCP logging.
 *
 * @internal
 */
class StdOutLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var array<string,int>
     */
    private array $levelMapping = [
        LogLevel::EMERGENCY => 7,
        LogLevel::ALERT => 6,
        LogLevel::CRITICAL => 5,
        LogLevel::ERROR => 4,
        LogLevel::WARNING => 3,
        LogLevel::NOTICE => 2,
        LogLevel::INFO => 1,
        LogLevel::DEBUG => 0,
    ];
    private int $level;

    /**
     * Constructs a basic PSR-3 logger class that logs into StdOut for GCP Logging
     *
     * @param string $level The level of the logger instance.
     */
    public function __construct(string $level = LogLevel::DEBUG)
    {
        $this->level = $this->getLevelFromName($level);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if ($this->getLevelFromName($level) < $this->level) {
            return;
        }

        print($message . "\n");
    }

    /**
     * @param string $levelName
     * @return int
     * @throws InvalidArgumentException
     */
    private function getLevelFromName(string $levelName): int
    {
        if (!array_key_exists($levelName, $this->levelMapping)) {
            throw new InvalidArgumentException('The level supplied to the Logger is not valid');
        }

        return $this->levelMapping[$levelName];
    }
}
