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

namespace Google\Service\TagManager;

class Destination extends \Google\Model
{
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * Destination ID.
   *
   * @var string
   */
  public $destinationId;
  /**
   * The Destination link ID uniquely identifies the Destination.
   *
   * @var string
   */
  public $destinationLinkId;
  /**
   * The fingerprint of the Google Tag Destination as computed at storage time.
   * This value is recomputed whenever the destination is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Destination display name.
   *
   * @var string
   */
  public $name;
  /**
   * Destination's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Auto generated link to the tag manager UI.
   *
   * @var string
   */
  public $tagManagerUrl;

  /**
   * GTM Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * GTM Container ID.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * Destination ID.
   *
   * @param string $destinationId
   */
  public function setDestinationId($destinationId)
  {
    $this->destinationId = $destinationId;
  }
  /**
   * @return string
   */
  public function getDestinationId()
  {
    return $this->destinationId;
  }
  /**
   * The Destination link ID uniquely identifies the Destination.
   *
   * @param string $destinationLinkId
   */
  public function setDestinationLinkId($destinationLinkId)
  {
    $this->destinationLinkId = $destinationLinkId;
  }
  /**
   * @return string
   */
  public function getDestinationLinkId()
  {
    return $this->destinationLinkId;
  }
  /**
   * The fingerprint of the Google Tag Destination as computed at storage time.
   * This value is recomputed whenever the destination is modified.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Destination display name.
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
   * Destination's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Auto generated link to the tag manager UI.
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Destination::class, 'Google_Service_TagManager_Destination');
