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

namespace Google\Service\ShoppingContent;

class AccountYouTubeChannelLink extends \Google\Model
{
  /**
   * Channel ID.
   *
   * @var string
   */
  public $channelId;
  /**
   * Status of the link between this Merchant Center account and the YouTube
   * channel. Upon retrieval, it represents the actual status of the link and
   * can be either `active` if it was approved in YT Creator Studio or `pending`
   * if it's pending approval. Upon insertion, it represents the *intended*
   * status of the link. Re-uploading a link with status `active` when it's
   * still pending or with status `pending` when it's already active will have
   * no effect: the status will remain unchanged. Re-uploading a link with
   * deprecated status `inactive` is equivalent to not submitting the link at
   * all and will delete the link if it was active or cancel the link request if
   * it was pending.
   *
   * @var string
   */
  public $status;

  /**
   * Channel ID.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * Status of the link between this Merchant Center account and the YouTube
   * channel. Upon retrieval, it represents the actual status of the link and
   * can be either `active` if it was approved in YT Creator Studio or `pending`
   * if it's pending approval. Upon insertion, it represents the *intended*
   * status of the link. Re-uploading a link with status `active` when it's
   * still pending or with status `pending` when it's already active will have
   * no effect: the status will remain unchanged. Re-uploading a link with
   * deprecated status `inactive` is equivalent to not submitting the link at
   * all and will delete the link if it was active or cancel the link request if
   * it was pending.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountYouTubeChannelLink::class, 'Google_Service_ShoppingContent_AccountYouTubeChannelLink');
