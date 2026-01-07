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

namespace Google\Service\Classroom;

class Registration extends \Google\Model
{
  protected $cloudPubsubTopicType = CloudPubsubTopic::class;
  protected $cloudPubsubTopicDataType = '';
  /**
   * The time until which the `Registration` is effective. This is a read-only
   * field assigned by the server.
   *
   * @var string
   */
  public $expiryTime;
  protected $feedType = Feed::class;
  protected $feedDataType = '';
  /**
   * A server-generated unique identifier for this `Registration`. Read-only.
   *
   * @var string
   */
  public $registrationId;

  /**
   * The Cloud Pub/Sub topic that notifications are to be sent to.
   *
   * @param CloudPubsubTopic $cloudPubsubTopic
   */
  public function setCloudPubsubTopic(CloudPubsubTopic $cloudPubsubTopic)
  {
    $this->cloudPubsubTopic = $cloudPubsubTopic;
  }
  /**
   * @return CloudPubsubTopic
   */
  public function getCloudPubsubTopic()
  {
    return $this->cloudPubsubTopic;
  }
  /**
   * The time until which the `Registration` is effective. This is a read-only
   * field assigned by the server.
   *
   * @param string $expiryTime
   */
  public function setExpiryTime($expiryTime)
  {
    $this->expiryTime = $expiryTime;
  }
  /**
   * @return string
   */
  public function getExpiryTime()
  {
    return $this->expiryTime;
  }
  /**
   * Specification for the class of notifications that Classroom should deliver
   * to the destination.
   *
   * @param Feed $feed
   */
  public function setFeed(Feed $feed)
  {
    $this->feed = $feed;
  }
  /**
   * @return Feed
   */
  public function getFeed()
  {
    return $this->feed;
  }
  /**
   * A server-generated unique identifier for this `Registration`. Read-only.
   *
   * @param string $registrationId
   */
  public function setRegistrationId($registrationId)
  {
    $this->registrationId = $registrationId;
  }
  /**
   * @return string
   */
  public function getRegistrationId()
  {
    return $this->registrationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Registration::class, 'Google_Service_Classroom_Registration');
