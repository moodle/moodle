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

class MessagePart extends \Google\Collection
{
  protected $collection_key = 'parts';
  protected $bodyType = MessagePartBody::class;
  protected $bodyDataType = '';
  /**
   * The filename of the attachment. Only present if this message part
   * represents an attachment.
   *
   * @var string
   */
  public $filename;
  protected $headersType = MessagePartHeader::class;
  protected $headersDataType = 'array';
  /**
   * The MIME type of the message part.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The immutable ID of the message part.
   *
   * @var string
   */
  public $partId;
  protected $partsType = MessagePart::class;
  protected $partsDataType = 'array';

  /**
   * The message part body for this part, which may be empty for container MIME
   * message parts.
   *
   * @param MessagePartBody $body
   */
  public function setBody(MessagePartBody $body)
  {
    $this->body = $body;
  }
  /**
   * @return MessagePartBody
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * The filename of the attachment. Only present if this message part
   * represents an attachment.
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * List of headers on this message part. For the top-level message part,
   * representing the entire message payload, it will contain the standard RFC
   * 2822 email headers such as `To`, `From`, and `Subject`.
   *
   * @param MessagePartHeader[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return MessagePartHeader[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * The MIME type of the message part.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The immutable ID of the message part.
   *
   * @param string $partId
   */
  public function setPartId($partId)
  {
    $this->partId = $partId;
  }
  /**
   * @return string
   */
  public function getPartId()
  {
    return $this->partId;
  }
  /**
   * The child MIME message parts of this part. This only applies to container
   * MIME message parts, for example `multipart`. For non- container MIME
   * message part types, such as `text/plain`, this field is empty. For more
   * information, see RFC 1521.
   *
   * @param MessagePart[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return MessagePart[]
   */
  public function getParts()
  {
    return $this->parts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MessagePart::class, 'Google_Service_Gmail_MessagePart');
