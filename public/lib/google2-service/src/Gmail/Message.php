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

class Message extends \Google\Collection
{
  protected $collection_key = 'labelIds';
  protected $classificationLabelValuesType = ClassificationLabelValue::class;
  protected $classificationLabelValuesDataType = 'array';
  /**
   * The ID of the last history record that modified this message.
   *
   * @var string
   */
  public $historyId;
  /**
   * The immutable ID of the message.
   *
   * @var string
   */
  public $id;
  /**
   * The internal message creation timestamp (epoch ms), which determines
   * ordering in the inbox. For normal SMTP-received email, this represents the
   * time the message was originally accepted by Google, which is more reliable
   * than the `Date` header. However, for API-migrated mail, it can be
   * configured by client to be based on the `Date` header.
   *
   * @var string
   */
  public $internalDate;
  /**
   * List of IDs of labels applied to this message.
   *
   * @var string[]
   */
  public $labelIds;
  protected $payloadType = MessagePart::class;
  protected $payloadDataType = '';
  /**
   * The entire email message in an RFC 2822 formatted and base64url encoded
   * string. Returned in `messages.get` and `drafts.get` responses when the
   * `format=RAW` parameter is supplied.
   *
   * @var string
   */
  public $raw;
  /**
   * Estimated size in bytes of the message.
   *
   * @var int
   */
  public $sizeEstimate;
  /**
   * A short part of the message text.
   *
   * @var string
   */
  public $snippet;
  /**
   * The ID of the thread the message belongs to. To add a message or draft to a
   * thread, the following criteria must be met: 1. The requested `threadId`
   * must be specified on the `Message` or `Draft.Message` you supply with your
   * request. 2. The `References` and `In-Reply-To` headers must be set in
   * compliance with the [RFC 2822](https://tools.ietf.org/html/rfc2822)
   * standard. 3. The `Subject` headers must match.
   *
   * @var string
   */
  public $threadId;

  /**
   * Classification Label values on the message. Available Classification Label
   * schemas can be queried using the Google Drive Labels API. Each
   * classification label ID must be unique. If duplicate IDs are provided, only
   * one will be retained, and the selection is arbitrary. Only used for Google
   * Workspace accounts.
   *
   * @param ClassificationLabelValue[] $classificationLabelValues
   */
  public function setClassificationLabelValues($classificationLabelValues)
  {
    $this->classificationLabelValues = $classificationLabelValues;
  }
  /**
   * @return ClassificationLabelValue[]
   */
  public function getClassificationLabelValues()
  {
    return $this->classificationLabelValues;
  }
  /**
   * The ID of the last history record that modified this message.
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
  /**
   * The immutable ID of the message.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The internal message creation timestamp (epoch ms), which determines
   * ordering in the inbox. For normal SMTP-received email, this represents the
   * time the message was originally accepted by Google, which is more reliable
   * than the `Date` header. However, for API-migrated mail, it can be
   * configured by client to be based on the `Date` header.
   *
   * @param string $internalDate
   */
  public function setInternalDate($internalDate)
  {
    $this->internalDate = $internalDate;
  }
  /**
   * @return string
   */
  public function getInternalDate()
  {
    return $this->internalDate;
  }
  /**
   * List of IDs of labels applied to this message.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * The parsed email structure in the message parts.
   *
   * @param MessagePart $payload
   */
  public function setPayload(MessagePart $payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return MessagePart
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * The entire email message in an RFC 2822 formatted and base64url encoded
   * string. Returned in `messages.get` and `drafts.get` responses when the
   * `format=RAW` parameter is supplied.
   *
   * @param string $raw
   */
  public function setRaw($raw)
  {
    $this->raw = $raw;
  }
  /**
   * @return string
   */
  public function getRaw()
  {
    return $this->raw;
  }
  /**
   * Estimated size in bytes of the message.
   *
   * @param int $sizeEstimate
   */
  public function setSizeEstimate($sizeEstimate)
  {
    $this->sizeEstimate = $sizeEstimate;
  }
  /**
   * @return int
   */
  public function getSizeEstimate()
  {
    return $this->sizeEstimate;
  }
  /**
   * A short part of the message text.
   *
   * @param string $snippet
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The ID of the thread the message belongs to. To add a message or draft to a
   * thread, the following criteria must be met: 1. The requested `threadId`
   * must be specified on the `Message` or `Draft.Message` you supply with your
   * request. 2. The `References` and `In-Reply-To` headers must be set in
   * compliance with the [RFC 2822](https://tools.ietf.org/html/rfc2822)
   * standard. 3. The `Subject` headers must match.
   *
   * @param string $threadId
   */
  public function setThreadId($threadId)
  {
    $this->threadId = $threadId;
  }
  /**
   * @return string
   */
  public function getThreadId()
  {
    return $this->threadId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_Gmail_Message');
