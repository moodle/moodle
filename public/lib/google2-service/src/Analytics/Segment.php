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

namespace Google\Service\Analytics;

class Segment extends \Google\Model
{
  /**
   * Time the segment was created.
   *
   * @var string
   */
  public $created;
  /**
   * Segment definition.
   *
   * @var string
   */
  public $definition;
  /**
   * Segment ID.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for Analytics segment.
   *
   * @var string
   */
  public $kind;
  /**
   * Segment name.
   *
   * @var string
   */
  public $name;
  /**
   * Segment ID. Can be used with the 'segment' parameter in Core Reporting API.
   *
   * @var string
   */
  public $segmentId;
  /**
   * Link for this segment.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Type for a segment. Possible values are "BUILT_IN" or "CUSTOM".
   *
   * @var string
   */
  public $type;
  /**
   * Time the segment was last modified.
   *
   * @var string
   */
  public $updated;

  /**
   * Time the segment was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Segment definition.
   *
   * @param string $definition
   */
  public function setDefinition($definition)
  {
    $this->definition = $definition;
  }
  /**
   * @return string
   */
  public function getDefinition()
  {
    return $this->definition;
  }
  /**
   * Segment ID.
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
   * Resource type for Analytics segment.
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
   * Segment name.
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
   * Segment ID. Can be used with the 'segment' parameter in Core Reporting API.
   *
   * @param string $segmentId
   */
  public function setSegmentId($segmentId)
  {
    $this->segmentId = $segmentId;
  }
  /**
   * @return string
   */
  public function getSegmentId()
  {
    return $this->segmentId;
  }
  /**
   * Link for this segment.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Type for a segment. Possible values are "BUILT_IN" or "CUSTOM".
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Time the segment was last modified.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Segment::class, 'Google_Service_Analytics_Segment');
