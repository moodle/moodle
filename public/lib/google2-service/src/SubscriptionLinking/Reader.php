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

namespace Google\Service\SubscriptionLinking;

class Reader extends \Google\Model
{
  /**
   * Output only. Time the publication reader was created and associated with a
   * Google user.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of the reader. The last part of ppid in the
   * resource name is the publisher provided id.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The SwG publication id that the reader's subscription linking
   * was originating from.
   *
   * @var string
   */
  public $originatingPublicationId;
  /**
   * Output only. The publisher provided id of the reader.
   *
   * @var string
   */
  public $ppid;
  /**
   * Output only. The SwG publication id that the reader has linked their
   * subscription to.
   *
   * @var string
   */
  public $publicationId;

  /**
   * Output only. Time the publication reader was created and associated with a
   * Google user.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The resource name of the reader. The last part of ppid in the
   * resource name is the publisher provided id.
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
   * Output only. The SwG publication id that the reader's subscription linking
   * was originating from.
   *
   * @param string $originatingPublicationId
   */
  public function setOriginatingPublicationId($originatingPublicationId)
  {
    $this->originatingPublicationId = $originatingPublicationId;
  }
  /**
   * @return string
   */
  public function getOriginatingPublicationId()
  {
    return $this->originatingPublicationId;
  }
  /**
   * Output only. The publisher provided id of the reader.
   *
   * @param string $ppid
   */
  public function setPpid($ppid)
  {
    $this->ppid = $ppid;
  }
  /**
   * @return string
   */
  public function getPpid()
  {
    return $this->ppid;
  }
  /**
   * Output only. The SwG publication id that the reader has linked their
   * subscription to.
   *
   * @param string $publicationId
   */
  public function setPublicationId($publicationId)
  {
    $this->publicationId = $publicationId;
  }
  /**
   * @return string
   */
  public function getPublicationId()
  {
    return $this->publicationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Reader::class, 'Google_Service_SubscriptionLinking_Reader');
