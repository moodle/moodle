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

namespace Google\Service\Gmail;

class MessagePartBody extends \Google\Model
{
  /**
   * When present, contains the ID of an external attachment that can be
   * retrieved in a separate `messages.attachments.get` request. When not
   * present, the entire content of the message part body is contained in the
   * data field.
   *
   * @var string
   */
  public $attachmentId;
  /**
   * The body data of a MIME message part as a base64url encoded string. May be
   * empty for MIME container types that have no message body or when the body
   * data is sent as a separate attachment. An attachment ID is present if the
   * body data is contained in a separate attachment.
   *
   * @var string
   */
  public $data;
  /**
   * Number of bytes for the message part data (encoding notwithstanding).
   *
   * @var int
   */
  public $size;

  /**
   * When present, contains the ID of an external attachment that can be
   * retrieved in a separate `messages.attachments.get` request. When not
   * present, the entire content of the message part body is contained in the
   * data field.
   *
   * @param string $attachmentId
   */
  public function setAttachmentId($attachmentId)
  {
    $this->attachmentId = $attachmentId;
  }
  /**
   * @return string
   */
  public function getAttachmentId()
  {
    return $this->attachmentId;
  }
  /**
   * The body data of a MIME message part as a base64url encoded string. May be
   * empty for MIME container types that have no message body or when the body
   * data is sent as a separate attachment. An attachment ID is present if the
   * body data is contained in a separate attachment.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Number of bytes for the message part data (encoding notwithstanding).
   *
   * @param int $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MessagePartBody::class, 'Google_Service_Gmail_MessagePartBody');
