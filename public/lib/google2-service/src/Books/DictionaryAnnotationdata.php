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

namespace Google\Service\Books;

class DictionaryAnnotationdata extends \Google\Model
{
  /**
   * The type of annotation this data is for.
   *
   * @var string
   */
  public $annotationType;
  protected $dataType = Dictlayerdata::class;
  protected $dataDataType = '';
  /**
   * Base64 encoded data for this annotation data.
   *
   * @var string
   */
  public $encodedData;
  /**
   * Unique id for this annotation data.
   *
   * @var string
   */
  public $id;
  /**
   * Resource Type
   *
   * @var string
   */
  public $kind;
  /**
   * The Layer id for this data. *
   *
   * @var string
   */
  public $layerId;
  /**
   * URL for this resource. *
   *
   * @var string
   */
  public $selfLink;
  /**
   * Timestamp for the last time this data was updated. (RFC 3339 UTC date-time
   * format).
   *
   * @var string
   */
  public $updated;
  /**
   * The volume id for this data. *
   *
   * @var string
   */
  public $volumeId;

  /**
   * The type of annotation this data is for.
   *
   * @param string $annotationType
   */
  public function setAnnotationType($annotationType)
  {
    $this->annotationType = $annotationType;
  }
  /**
   * @return string
   */
  public function getAnnotationType()
  {
    return $this->annotationType;
  }
  /**
   * JSON encoded data for this dictionary annotation data. Emitted with name
   * 'data' in JSON output. Either this or geo_data will be populated.
   *
   * @param Dictlayerdata $data
   */
  public function setData(Dictlayerdata $data)
  {
    $this->data = $data;
  }
  /**
   * @return Dictlayerdata
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Base64 encoded data for this annotation data.
   *
   * @param string $encodedData
   */
  public function setEncodedData($encodedData)
  {
    $this->encodedData = $encodedData;
  }
  /**
   * @return string
   */
  public function getEncodedData()
  {
    return $this->encodedData;
  }
  /**
   * Unique id for this annotation data.
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
   * Resource Type
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
   * The Layer id for this data. *
   *
   * @param string $layerId
   */
  public function setLayerId($layerId)
  {
    $this->layerId = $layerId;
  }
  /**
   * @return string
   */
  public function getLayerId()
  {
    return $this->layerId;
  }
  /**
   * URL for this resource. *
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
   * Timestamp for the last time this data was updated. (RFC 3339 UTC date-time
   * format).
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
  /**
   * The volume id for this data. *
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DictionaryAnnotationdata::class, 'Google_Service_Books_DictionaryAnnotationdata');
