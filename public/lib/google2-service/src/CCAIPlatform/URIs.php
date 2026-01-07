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

namespace Google\Service\CCAIPlatform;

class URIs extends \Google\Model
{
  /**
   * Chat Bot Uri of the ContactCenter
   *
   * @var string
   */
  public $chatBotUri;
  /**
   * Media Uri of the ContactCenter.
   *
   * @var string
   */
  public $mediaUri;
  /**
   * Root Uri of the ContactCenter.
   *
   * @var string
   */
  public $rootUri;
  /**
   * Virtual Agent Streaming Service Uri of the ContactCenter.
   *
   * @var string
   */
  public $virtualAgentStreamingServiceUri;

  /**
   * Chat Bot Uri of the ContactCenter
   *
   * @param string $chatBotUri
   */
  public function setChatBotUri($chatBotUri)
  {
    $this->chatBotUri = $chatBotUri;
  }
  /**
   * @return string
   */
  public function getChatBotUri()
  {
    return $this->chatBotUri;
  }
  /**
   * Media Uri of the ContactCenter.
   *
   * @param string $mediaUri
   */
  public function setMediaUri($mediaUri)
  {
    $this->mediaUri = $mediaUri;
  }
  /**
   * @return string
   */
  public function getMediaUri()
  {
    return $this->mediaUri;
  }
  /**
   * Root Uri of the ContactCenter.
   *
   * @param string $rootUri
   */
  public function setRootUri($rootUri)
  {
    $this->rootUri = $rootUri;
  }
  /**
   * @return string
   */
  public function getRootUri()
  {
    return $this->rootUri;
  }
  /**
   * Virtual Agent Streaming Service Uri of the ContactCenter.
   *
   * @param string $virtualAgentStreamingServiceUri
   */
  public function setVirtualAgentStreamingServiceUri($virtualAgentStreamingServiceUri)
  {
    $this->virtualAgentStreamingServiceUri = $virtualAgentStreamingServiceUri;
  }
  /**
   * @return string
   */
  public function getVirtualAgentStreamingServiceUri()
  {
    return $this->virtualAgentStreamingServiceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(URIs::class, 'Google_Service_CCAIPlatform_URIs');
