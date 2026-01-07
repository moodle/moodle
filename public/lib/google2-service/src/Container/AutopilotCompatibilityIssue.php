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

namespace Google\Service\Container;

class AutopilotCompatibilityIssue extends \Google\Collection
{
  /**
   * Default value, should not be used.
   */
  public const INCOMPATIBILITY_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Indicates that the issue is a known incompatibility between the cluster and
   * Autopilot mode.
   */
  public const INCOMPATIBILITY_TYPE_INCOMPATIBILITY = 'INCOMPATIBILITY';
  /**
   * Indicates the issue is an incompatibility if customers take no further
   * action to resolve.
   */
  public const INCOMPATIBILITY_TYPE_ADDITIONAL_CONFIG_REQUIRED = 'ADDITIONAL_CONFIG_REQUIRED';
  /**
   * Indicates the issue is not an incompatibility, but depending on the
   * workloads business logic, there is a potential that they won't work on
   * Autopilot.
   */
  public const INCOMPATIBILITY_TYPE_PASSED_WITH_OPTIONAL_CONFIG = 'PASSED_WITH_OPTIONAL_CONFIG';
  protected $collection_key = 'subjects';
  /**
   * The constraint type of the issue.
   *
   * @var string
   */
  public $constraintType;
  /**
   * The description of the issue.
   *
   * @var string
   */
  public $description;
  /**
   * A URL to a public documentation, which addresses resolving this issue.
   *
   * @var string
   */
  public $documentationUrl;
  /**
   * The incompatibility type of this issue.
   *
   * @var string
   */
  public $incompatibilityType;
  /**
   * The last time when this issue was observed.
   *
   * @var string
   */
  public $lastObservation;
  /**
   * The name of the resources which are subject to this issue.
   *
   * @var string[]
   */
  public $subjects;

  /**
   * The constraint type of the issue.
   *
   * @param string $constraintType
   */
  public function setConstraintType($constraintType)
  {
    $this->constraintType = $constraintType;
  }
  /**
   * @return string
   */
  public function getConstraintType()
  {
    return $this->constraintType;
  }
  /**
   * The description of the issue.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A URL to a public documentation, which addresses resolving this issue.
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
   * The incompatibility type of this issue.
   *
   * Accepted values: UNSPECIFIED, INCOMPATIBILITY, ADDITIONAL_CONFIG_REQUIRED,
   * PASSED_WITH_OPTIONAL_CONFIG
   *
   * @param self::INCOMPATIBILITY_TYPE_* $incompatibilityType
   */
  public function setIncompatibilityType($incompatibilityType)
  {
    $this->incompatibilityType = $incompatibilityType;
  }
  /**
   * @return self::INCOMPATIBILITY_TYPE_*
   */
  public function getIncompatibilityType()
  {
    return $this->incompatibilityType;
  }
  /**
   * The last time when this issue was observed.
   *
   * @param string $lastObservation
   */
  public function setLastObservation($lastObservation)
  {
    $this->lastObservation = $lastObservation;
  }
  /**
   * @return string
   */
  public function getLastObservation()
  {
    return $this->lastObservation;
  }
  /**
   * The name of the resources which are subject to this issue.
   *
   * @param string[] $subjects
   */
  public function setSubjects($subjects)
  {
    $this->subjects = $subjects;
  }
  /**
   * @return string[]
   */
  public function getSubjects()
  {
    return $this->subjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutopilotCompatibilityIssue::class, 'Google_Service_Container_AutopilotCompatibilityIssue');
