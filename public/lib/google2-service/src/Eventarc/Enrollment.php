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

namespace Google\Service\Eventarc;

class Enrollment extends \Google\Model
{
  /**
   * Optional. Resource annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Required. A CEL expression identifying which messages this enrollment
   * applies to.
   *
   * @var string
   */
  public $celMatch;
  /**
   * Output only. The creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Destination is the Pipeline that the Enrollment is delivering to.
   * It must point to the full resource name of a Pipeline. Format:
   * "projects/{PROJECT_ID}/locations/{region}/pipelines/{PIPELINE_ID)"
   *
   * @var string
   */
  public $destination;
  /**
   * Optional. Resource display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on update and delete requests to
   * ensure that the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Resource labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Immutable. Resource name of the message bus identifying the
   * source of the messages. It matches the form
   * projects/{project}/locations/{location}/messageBuses/{messageBus}.
   *
   * @var string
   */
  public $messageBus;
  /**
   * Identifier. Resource name of the form
   * projects/{project}/locations/{location}/enrollments/{enrollment}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Server assigned unique identifier for the channel. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Resource annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Required. A CEL expression identifying which messages this enrollment
   * applies to.
   *
   * @param string $celMatch
   */
  public function setCelMatch($celMatch)
  {
    $this->celMatch = $celMatch;
  }
  /**
   * @return string
   */
  public function getCelMatch()
  {
    return $this->celMatch;
  }
  /**
   * Output only. The creation time.
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
   * Required. Destination is the Pipeline that the Enrollment is delivering to.
   * It must point to the full resource name of a Pipeline. Format:
   * "projects/{PROJECT_ID}/locations/{region}/pipelines/{PIPELINE_ID)"
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Optional. Resource display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on update and delete requests to
   * ensure that the client has an up-to-date value before proceeding.
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
   * Optional. Resource labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. Immutable. Resource name of the message bus identifying the
   * source of the messages. It matches the form
   * projects/{project}/locations/{location}/messageBuses/{messageBus}.
   *
   * @param string $messageBus
   */
  public function setMessageBus($messageBus)
  {
    $this->messageBus = $messageBus;
  }
  /**
   * @return string
   */
  public function getMessageBus()
  {
    return $this->messageBus;
  }
  /**
   * Identifier. Resource name of the form
   * projects/{project}/locations/{location}/enrollments/{enrollment}
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
   * Output only. Server assigned unique identifier for the channel. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The last-modified time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Enrollment::class, 'Google_Service_Eventarc_Enrollment');
