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

namespace Google\Service\YouTube;

class LiveStreamContentDetails extends \Google\Model
{
  /**
   * The ingestion URL where the closed captions of this stream are sent.
   *
   * @var string
   */
  public $closedCaptionsIngestionUrl;
  /**
   * Indicates whether the stream is reusable, which means that it can be bound
   * to multiple broadcasts. It is common for broadcasters to reuse the same
   * stream for many different broadcasts if those broadcasts occur at different
   * times. If you set this value to false, then the stream will not be
   * reusable, which means that it can only be bound to one broadcast. Non-
   * reusable streams differ from reusable streams in the following ways: - A
   * non-reusable stream can only be bound to one broadcast. - A non-reusable
   * stream might be deleted by an automated process after the broadcast ends. -
   * The liveStreams.list method does not list non-reusable streams if you call
   * the method and set the mine parameter to true. The only way to use that
   * method to retrieve the resource for a non-reusable stream is to use the id
   * parameter to identify the stream.
   *
   * @var bool
   */
  public $isReusable;

  /**
   * The ingestion URL where the closed captions of this stream are sent.
   *
   * @param string $closedCaptionsIngestionUrl
   */
  public function setClosedCaptionsIngestionUrl($closedCaptionsIngestionUrl)
  {
    $this->closedCaptionsIngestionUrl = $closedCaptionsIngestionUrl;
  }
  /**
   * @return string
   */
  public function getClosedCaptionsIngestionUrl()
  {
    return $this->closedCaptionsIngestionUrl;
  }
  /**
   * Indicates whether the stream is reusable, which means that it can be bound
   * to multiple broadcasts. It is common for broadcasters to reuse the same
   * stream for many different broadcasts if those broadcasts occur at different
   * times. If you set this value to false, then the stream will not be
   * reusable, which means that it can only be bound to one broadcast. Non-
   * reusable streams differ from reusable streams in the following ways: - A
   * non-reusable stream can only be bound to one broadcast. - A non-reusable
   * stream might be deleted by an automated process after the broadcast ends. -
   * The liveStreams.list method does not list non-reusable streams if you call
   * the method and set the mine parameter to true. The only way to use that
   * method to retrieve the resource for a non-reusable stream is to use the id
   * parameter to identify the stream.
   *
   * @param bool $isReusable
   */
  public function setIsReusable($isReusable)
  {
    $this->isReusable = $isReusable;
  }
  /**
   * @return bool
   */
  public function getIsReusable()
  {
    return $this->isReusable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveStreamContentDetails::class, 'Google_Service_YouTube_LiveStreamContentDetails');
