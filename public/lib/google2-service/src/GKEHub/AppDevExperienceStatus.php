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

namespace Google\Service\GKEHub;

class AppDevExperienceStatus extends \Google\Model
{
  /**
   * Not set.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * AppDevExperienceFeature's specified subcomponent is ready.
   */
  public const CODE_OK = 'OK';
  /**
   * AppDevExperienceFeature's specified subcomponent ready state is false. This
   * means AppDevExperienceFeature has encountered an issue that blocks all, or
   * a portion, of its normal operation. See the `description` for more details.
   */
  public const CODE_FAILED = 'FAILED';
  /**
   * AppDevExperienceFeature's specified subcomponent has a pending or unknown
   * state.
   */
  public const CODE_UNKNOWN = 'UNKNOWN';
  /**
   * Code specifies AppDevExperienceFeature's subcomponent ready state.
   *
   * @var string
   */
  public $code;
  /**
   * Description is populated if Code is Failed, explaining why it has failed.
   *
   * @var string
   */
  public $description;

  /**
   * Code specifies AppDevExperienceFeature's subcomponent ready state.
   *
   * Accepted values: CODE_UNSPECIFIED, OK, FAILED, UNKNOWN
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Description is populated if Code is Failed, explaining why it has failed.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppDevExperienceStatus::class, 'Google_Service_GKEHub_AppDevExperienceStatus');
