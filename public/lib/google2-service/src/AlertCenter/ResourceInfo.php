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

namespace Google\Service\AlertCenter;

class ResourceInfo extends \Google\Model
{
  /**
   * Chat attachment ID.
   *
   * @var string
   */
  public $chatAttachmentId;
  /**
   * Chat message ID.
   *
   * @var string
   */
  public $chatMessageId;
  /**
   * Id to identify a device. For example, for Android devices, this is the
   * "Android Device Id" and for Chrome OS devices, it's the "Device Virtual
   * Id".
   *
   * @var string
   */
  public $deviceId;
  /**
   * Drive file ID.
   *
   * @var string
   */
  public $documentId;
  /**
   * RFC2822 message ID.
   *
   * @var string
   */
  public $messageId;
  /**
   * Title of the resource, for example email subject, or document title.
   *
   * @var string
   */
  public $resourceTitle;

  /**
   * Chat attachment ID.
   *
   * @param string $chatAttachmentId
   */
  public function setChatAttachmentId($chatAttachmentId)
  {
    $this->chatAttachmentId = $chatAttachmentId;
  }
  /**
   * @return string
   */
  public function getChatAttachmentId()
  {
    return $this->chatAttachmentId;
  }
  /**
   * Chat message ID.
   *
   * @param string $chatMessageId
   */
  public function setChatMessageId($chatMessageId)
  {
    $this->chatMessageId = $chatMessageId;
  }
  /**
   * @return string
   */
  public function getChatMessageId()
  {
    return $this->chatMessageId;
  }
  /**
   * Id to identify a device. For example, for Android devices, this is the
   * "Android Device Id" and for Chrome OS devices, it's the "Device Virtual
   * Id".
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Drive file ID.
   *
   * @param string $documentId
   */
  public function setDocumentId($documentId)
  {
    $this->documentId = $documentId;
  }
  /**
   * @return string
   */
  public function getDocumentId()
  {
    return $this->documentId;
  }
  /**
   * RFC2822 message ID.
   *
   * @param string $messageId
   */
  public function setMessageId($messageId)
  {
    $this->messageId = $messageId;
  }
  /**
   * @return string
   */
  public function getMessageId()
  {
    return $this->messageId;
  }
  /**
   * Title of the resource, for example email subject, or document title.
   *
   * @param string $resourceTitle
   */
  public function setResourceTitle($resourceTitle)
  {
    $this->resourceTitle = $resourceTitle;
  }
  /**
   * @return string
   */
  public function getResourceTitle()
  {
    return $this->resourceTitle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceInfo::class, 'Google_Service_AlertCenter_ResourceInfo');
