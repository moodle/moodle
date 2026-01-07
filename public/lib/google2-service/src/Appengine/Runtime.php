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

namespace Google\Service\Appengine;

class Runtime extends \Google\Collection
{
  /**
   * Default value.
   */
  public const ENVIRONMENT_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * App Engine Standard.
   */
  public const ENVIRONMENT_STANDARD = 'STANDARD';
  /**
   * App Engine Flexible
   */
  public const ENVIRONMENT_FLEXIBLE = 'FLEXIBLE';
  /**
   * Not specified.
   */
  public const STAGE_RUNTIME_STAGE_UNSPECIFIED = 'RUNTIME_STAGE_UNSPECIFIED';
  /**
   * The runtime is in development.
   */
  public const STAGE_DEVELOPMENT = 'DEVELOPMENT';
  /**
   * The runtime is in the Alpha stage.
   */
  public const STAGE_ALPHA = 'ALPHA';
  /**
   * The runtime is in the Beta stage.
   */
  public const STAGE_BETA = 'BETA';
  /**
   * The runtime is generally available.
   */
  public const STAGE_GA = 'GA';
  /**
   * The runtime is deprecated.
   */
  public const STAGE_DEPRECATED = 'DEPRECATED';
  /**
   * The runtime is no longer supported.
   */
  public const STAGE_DECOMMISSIONED = 'DECOMMISSIONED';
  /**
   * The runtime is end of support.
   */
  public const STAGE_END_OF_SUPPORT = 'END_OF_SUPPORT';
  protected $collection_key = 'warnings';
  protected $decommissionedDateType = Date::class;
  protected $decommissionedDateDataType = '';
  protected $deprecationDateType = Date::class;
  protected $deprecationDateDataType = '';
  /**
   * User-friendly display name, e.g. 'Node.js 12', etc.
   *
   * @var string
   */
  public $displayName;
  protected $endOfSupportDateType = Date::class;
  protected $endOfSupportDateDataType = '';
  /**
   * The environment of the runtime.
   *
   * @var string
   */
  public $environment;
  /**
   * The name of the runtime, e.g., 'go113', 'nodejs12', etc.
   *
   * @var string
   */
  public $name;
  /**
   * The stage of life this runtime is in, e.g., BETA, GA, etc.
   *
   * @var string
   */
  public $stage;
  /**
   * Supported operating systems for the runtime, e.g., 'ubuntu22', etc.
   *
   * @var string[]
   */
  public $supportedOperatingSystems;
  /**
   * Warning messages, e.g., a deprecation warning.
   *
   * @var string[]
   */
  public $warnings;

  /**
   * Date when Runtime is decommissioned.
   *
   * @param Date $decommissionedDate
   */
  public function setDecommissionedDate(Date $decommissionedDate)
  {
    $this->decommissionedDate = $decommissionedDate;
  }
  /**
   * @return Date
   */
  public function getDecommissionedDate()
  {
    return $this->decommissionedDate;
  }
  /**
   * Date when Runtime is deprecated.
   *
   * @param Date $deprecationDate
   */
  public function setDeprecationDate(Date $deprecationDate)
  {
    $this->deprecationDate = $deprecationDate;
  }
  /**
   * @return Date
   */
  public function getDeprecationDate()
  {
    return $this->deprecationDate;
  }
  /**
   * User-friendly display name, e.g. 'Node.js 12', etc.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Date when Runtime is end of support.
   *
   * @param Date $endOfSupportDate
   */
  public function setEndOfSupportDate(Date $endOfSupportDate)
  {
    $this->endOfSupportDate = $endOfSupportDate;
  }
  /**
   * @return Date
   */
  public function getEndOfSupportDate()
  {
    return $this->endOfSupportDate;
  }
  /**
   * The environment of the runtime.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, STANDARD, FLEXIBLE
   *
   * @param self::ENVIRONMENT_* $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return self::ENVIRONMENT_*
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * The name of the runtime, e.g., 'go113', 'nodejs12', etc.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The stage of life this runtime is in, e.g., BETA, GA, etc.
   *
   * Accepted values: RUNTIME_STAGE_UNSPECIFIED, DEVELOPMENT, ALPHA, BETA, GA,
   * DEPRECATED, DECOMMISSIONED, END_OF_SUPPORT
   *
   * @param self::STAGE_* $stage
   */
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
  /**
   * @return self::STAGE_*
   */
  public function getStage()
  {
    return $this->stage;
  }
  /**
   * Supported operating systems for the runtime, e.g., 'ubuntu22', etc.
   *
   * @param string[] $supportedOperatingSystems
   */
  public function setSupportedOperatingSystems($supportedOperatingSystems)
  {
    $this->supportedOperatingSystems = $supportedOperatingSystems;
  }
  /**
   * @return string[]
   */
  public function getSupportedOperatingSystems()
  {
    return $this->supportedOperatingSystems;
  }
  /**
   * Warning messages, e.g., a deprecation warning.
   *
   * @param string[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return string[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Runtime::class, 'Google_Service_Appengine_Runtime');
