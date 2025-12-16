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

namespace Google\Service\Indexing;

class UrlNotification extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_URL_NOTIFICATION_TYPE_UNSPECIFIED = 'URL_NOTIFICATION_TYPE_UNSPECIFIED';
  /**
   * The given URL (Web document) has been updated.
   */
  public const TYPE_URL_UPDATED = 'URL_UPDATED';
  /**
   * The given URL (Web document) has been deleted.
   */
  public const TYPE_URL_DELETED = 'URL_DELETED';
  /**
   * Creation timestamp for this notification. Users should _not_ specify it,
   * the field is ignored at the request time.
   *
   * @var string
   */
  public $notifyTime;
  /**
   * The URL life cycle event that Google is being notified about.
   *
   * @var string
   */
  public $type;
  /**
   * The object of this notification. The URL must be owned by the publisher of
   * this notification and, in case of `URL_UPDATED` notifications, it _must_ be
   * crawlable by Google.
   *
   * @var string
   */
  public $url;

  /**
   * Creation timestamp for this notification. Users should _not_ specify it,
   * the field is ignored at the request time.
   *
   * @param string $notifyTime
   */
  public function setNotifyTime($notifyTime)
  {
    $this->notifyTime = $notifyTime;
  }
  /**
   * @return string
   */
  public function getNotifyTime()
  {
    return $this->notifyTime;
  }
  /**
   * The URL life cycle event that Google is being notified about.
   *
   * Accepted values: URL_NOTIFICATION_TYPE_UNSPECIFIED, URL_UPDATED,
   * URL_DELETED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The object of this notification. The URL must be owned by the publisher of
   * this notification and, in case of `URL_UPDATED` notifications, it _must_ be
   * crawlable by Google.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlNotification::class, 'Google_Service_Indexing_UrlNotification');
