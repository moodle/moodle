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

namespace Google\Service\WorkloadManager;

class ExecutionResult extends \Google\Collection
{
  /**
   * Unknown state
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * resource successfully passed the rule
   */
  public const TYPE_TYPE_PASSED = 'TYPE_PASSED';
  /**
   * resource violated the rule
   */
  public const TYPE_TYPE_VIOLATED = 'TYPE_VIOLATED';
  protected $collection_key = 'commands';
  protected $commandsType = Command::class;
  protected $commandsDataType = 'array';
  /**
   * The URL for the documentation of the rule.
   *
   * @var string
   */
  public $documentationUrl;
  protected $resourceType = WorkloadmanagerResource::class;
  protected $resourceDataType = '';
  /**
   * The rule that is violated in an evaluation.
   *
   * @var string
   */
  public $rule;
  /**
   * The severity of violation.
   *
   * @var string
   */
  public $severity;
  /**
   * Execution result type of the scanned resource
   *
   * @var string
   */
  public $type;
  protected $violationDetailsType = ViolationDetails::class;
  protected $violationDetailsDataType = '';
  /**
   * The violation message of an execution.
   *
   * @var string
   */
  public $violationMessage;

  /**
   * The commands to remediate the violation.
   *
   * @param Command[] $commands
   */
  public function setCommands($commands)
  {
    $this->commands = $commands;
  }
  /**
   * @return Command[]
   */
  public function getCommands()
  {
    return $this->commands;
  }
  /**
   * The URL for the documentation of the rule.
   *
   * @param string $documentationUrl
   */
  public function setDocumentationUrl($documentationUrl)
  {
    $this->documentationUrl = $documentationUrl;
  }
  /**
   * @return string
   */
  public function getDocumentationUrl()
  {
    return $this->documentationUrl;
  }
  /**
   * The resource that violates the rule.
   *
   * @param WorkloadmanagerResource $resource
   */
  public function setResource(WorkloadmanagerResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return WorkloadmanagerResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The rule that is violated in an evaluation.
   *
   * @param string $rule
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return string
   */
  public function getRule()
  {
    return $this->rule;
  }
  /**
   * The severity of violation.
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Execution result type of the scanned resource
   *
   * Accepted values: TYPE_UNSPECIFIED, TYPE_PASSED, TYPE_VIOLATED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The details of violation in an evaluation result.
   *
   * @param ViolationDetails $violationDetails
   */
  public function setViolationDetails(ViolationDetails $violationDetails)
  {
    $this->violationDetails = $violationDetails;
  }
  /**
   * @return ViolationDetails
   */
  public function getViolationDetails()
  {
    return $this->violationDetails;
  }
  /**
   * The violation message of an execution.
   *
   * @param string $violationMessage
   */
  public function setViolationMessage($violationMessage)
  {
    $this->violationMessage = $violationMessage;
  }
  /**
   * @return string
   */
  public function getViolationMessage()
  {
    return $this->violationMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutionResult::class, 'Google_Service_WorkloadManager_ExecutionResult');
