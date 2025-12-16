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

class Subscription extends \Google\Model
{
  protected $contentDetailsType = SubscriptionContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the subscription.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#subscription".
   *
   * @var string
   */
  public $kind;
  protected $snippetType = SubscriptionSnippet::class;
  protected $snippetDataType = '';
  protected $subscriberSnippetType = SubscriptionSubscriberSnippet::class;
  protected $subscriberSnippetDataType = '';

  /**
   * The contentDetails object contains basic statistics about the subscription.
   *
   * @param SubscriptionContentDetails $contentDetails
   */
  public function setContentDetails(SubscriptionContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return SubscriptionContentDetails
   */
  public function getContentDetails()
  {
    return $this->contentDetails;
  }
  /**
   * Etag of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The ID that YouTube uses to uniquely identify the subscription.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#subscription".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The snippet object contains basic details about the subscription, including
   * its title and the channel that the user subscribed to.
   *
   * @param SubscriptionSnippet $snippet
   */
  public function setSnippet(SubscriptionSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return SubscriptionSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The subscriberSnippet object contains basic details about the subscriber.
   *
   * @param SubscriptionSubscriberSnippet $subscriberSnippet
   */
  public function setSubscriberSnippet(SubscriptionSubscriberSnippet $subscriberSnippet)
  {
    $this->subscriberSnippet = $subscriberSnippet;
  }
  /**
   * @return SubscriptionSubscriberSnippet
   */
  public function getSubscriberSnippet()
  {
    return $this->subscriberSnippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscription::class, 'Google_Service_YouTube_Subscription');
