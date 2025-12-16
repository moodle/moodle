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

class FilterCriteria extends \Google\Model
{
  public const SIZE_COMPARISON_unspecified = 'unspecified';
  /**
   * Find messages smaller than the given size.
   */
  public const SIZE_COMPARISON_smaller = 'smaller';
  /**
   * Find messages larger than the given size.
   */
  public const SIZE_COMPARISON_larger = 'larger';
  /**
   * Whether the response should exclude chats.
   *
   * @var bool
   */
  public $excludeChats;
  /**
   * The sender's display name or email address.
   *
   * @var string
   */
  public $from;
  /**
   * Whether the message has any attachment.
   *
   * @var bool
   */
  public $hasAttachment;
  /**
   * Only return messages not matching the specified query. Supports the same
   * query format as the Gmail search box. For example,
   * `"from:someuser@example.com rfc822msgid: is:unread"`.
   *
   * @var string
   */
  public $negatedQuery;
  /**
   * Only return messages matching the specified query. Supports the same query
   * format as the Gmail search box. For example, `"from:someuser@example.com
   * rfc822msgid: is:unread"`.
   *
   * @var string
   */
  public $query;
  /**
   * The size of the entire RFC822 message in bytes, including all headers and
   * attachments.
   *
   * @var int
   */
  public $size;
  /**
   * How the message size in bytes should be in relation to the size field.
   *
   * @var string
   */
  public $sizeComparison;
  /**
   * Case-insensitive phrase found in the message's subject. Trailing and
   * leading whitespace are be trimmed and adjacent spaces are collapsed.
   *
   * @var string
   */
  public $subject;
  /**
   * The recipient's display name or email address. Includes recipients in the
   * "to", "cc", and "bcc" header fields. You can use simply the local part of
   * the email address. For example, "example" and "example@" both match
   * "example@gmail.com". This field is case-insensitive.
   *
   * @var string
   */
  public $to;

  /**
   * Whether the response should exclude chats.
   *
   * @param bool $excludeChats
   */
  public function setExcludeChats($excludeChats)
  {
    $this->excludeChats = $excludeChats;
  }
  /**
   * @return bool
   */
  public function getExcludeChats()
  {
    return $this->excludeChats;
  }
  /**
   * The sender's display name or email address.
   *
   * @param string $from
   */
  public function setFrom($from)
  {
    $this->from = $from;
  }
  /**
   * @return string
   */
  public function getFrom()
  {
    return $this->from;
  }
  /**
   * Whether the message has any attachment.
   *
   * @param bool $hasAttachment
   */
  public function setHasAttachment($hasAttachment)
  {
    $this->hasAttachment = $hasAttachment;
  }
  /**
   * @return bool
   */
  public function getHasAttachment()
  {
    return $this->hasAttachment;
  }
  /**
   * Only return messages not matching the specified query. Supports the same
   * query format as the Gmail search box. For example,
   * `"from:someuser@example.com rfc822msgid: is:unread"`.
   *
   * @param string $negatedQuery
   */
  public function setNegatedQuery($negatedQuery)
  {
    $this->negatedQuery = $negatedQuery;
  }
  /**
   * @return string
   */
  public function getNegatedQuery()
  {
    return $this->negatedQuery;
  }
  /**
   * Only return messages matching the specified query. Supports the same query
   * format as the Gmail search box. For example, `"from:someuser@example.com
   * rfc822msgid: is:unread"`.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The size of the entire RFC822 message in bytes, including all headers and
   * attachments.
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
  /**
   * How the message size in bytes should be in relation to the size field.
   *
   * Accepted values: unspecified, smaller, larger
   *
   * @param self::SIZE_COMPARISON_* $sizeComparison
   */
  public function setSizeComparison($sizeComparison)
  {
    $this->sizeComparison = $sizeComparison;
  }
  /**
   * @return self::SIZE_COMPARISON_*
   */
  public function getSizeComparison()
  {
    return $this->sizeComparison;
  }
  /**
   * Case-insensitive phrase found in the message's subject. Trailing and
   * leading whitespace are be trimmed and adjacent spaces are collapsed.
   *
   * @param string $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }
  /**
   * The recipient's display name or email address. Includes recipients in the
   * "to", "cc", and "bcc" header fields. You can use simply the local part of
   * the email address. For example, "example" and "example@" both match
   * "example@gmail.com". This field is case-insensitive.
   *
   * @param string $to
   */
  public function setTo($to)
  {
    $this->to = $to;
  }
  /**
   * @return string
   */
  public function getTo()
  {
    return $this->to;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterCriteria::class, 'Google_Service_Gmail_FilterCriteria');
