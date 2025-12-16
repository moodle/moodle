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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1CorpusStatus extends \Google\Model
{
  /**
   * This state is not supposed to happen.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * RagCorpus resource entry is initialized, but hasn't done validation.
   */
  public const STATE_INITIALIZED = 'INITIALIZED';
  /**
   * RagCorpus is provisioned successfully and is ready to serve.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * RagCorpus is in a problematic situation. See `error_message` field for
   * details.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Output only. Only when the `state` field is ERROR.
   *
   * @var string
   */
  public $errorStatus;
  /**
   * Output only. RagCorpus life state.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Only when the `state` field is ERROR.
   *
   * @param string $errorStatus
   */
  public function setErrorStatus($errorStatus)
  {
    $this->errorStatus = $errorStatus;
  }
  /**
   * @return string
   */
  public function getErrorStatus()
  {
    return $this->errorStatus;
  }
  /**
   * Output only. RagCorpus life state.
   *
   * Accepted values: UNKNOWN, INITIALIZED, ACTIVE, ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CorpusStatus::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CorpusStatus');
