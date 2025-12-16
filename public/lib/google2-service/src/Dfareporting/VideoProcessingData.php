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

namespace Google\Service\Dfareporting;

class VideoProcessingData extends \Google\Model
{
  /**
   * The processing state is unknown.
   */
  public const PROCESSING_STATE_UNKNOWN = 'UNKNOWN';
  /**
   * The asset is being processed.
   */
  public const PROCESSING_STATE_PROCESSING = 'PROCESSING';
  /**
   * The asset was successfully processed.
   */
  public const PROCESSING_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The asset failed to be processed.
   */
  public const PROCESSING_STATE_FAILED = 'FAILED';
  /**
   * For a FAILED processing state, the error reason discovered.
   *
   * @var string
   */
  public $errorReason;
  /**
   * Output only. The processing state of the studio creative asset.
   *
   * @var string
   */
  public $processingState;

  /**
   * For a FAILED processing state, the error reason discovered.
   *
   * @param string $errorReason
   */
  public function setErrorReason($errorReason)
  {
    $this->errorReason = $errorReason;
  }
  /**
   * @return string
   */
  public function getErrorReason()
  {
    return $this->errorReason;
  }
  /**
   * Output only. The processing state of the studio creative asset.
   *
   * Accepted values: UNKNOWN, PROCESSING, SUCCEEDED, FAILED
   *
   * @param self::PROCESSING_STATE_* $processingState
   */
  public function setProcessingState($processingState)
  {
    $this->processingState = $processingState;
  }
  /**
   * @return self::PROCESSING_STATE_*
   */
  public function getProcessingState()
  {
    return $this->processingState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoProcessingData::class, 'Google_Service_Dfareporting_VideoProcessingData');
