<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudComposer;

class ExecuteAirflowCommandRequest extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * Airflow command.
   *
   * @var string
   */
  public $command;
  /**
   * Parameters for the Airflow command/subcommand as an array of arguments. It
   * may contain positional arguments like `["my-dag-id"]`, key-value parameters
   * like `["--foo=bar"]` or `["--foo","bar"]`, or other flags like `["-f"]`.
   *
   * @var string[]
   */
  public $parameters;
  /**
   * Airflow subcommand.
   *
   * @var string
   */
  public $subcommand;

  /**
   * Airflow command.
   *
   * @param string $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Parameters for the Airflow command/subcommand as an array of arguments. It
   * may contain positional arguments like `["my-dag-id"]`, key-value parameters
   * like `["--foo=bar"]` or `["--foo","bar"]`, or other flags like `["-f"]`.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Airflow subcommand.
   *
   * @param string $subcommand
   */
  public function setSubcommand($subcommand)
  {
    $this->subcommand = $subcommand;
  }
  /**
   * @return string
   */
  public function getSubcommand()
  {
    return $this->subcommand;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecuteAirflowCommandRequest::class, 'Google_Service_CloudComposer_ExecuteAirflowCommandRequest');
