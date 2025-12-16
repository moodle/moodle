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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Aspect extends \Google\Model
{
  protected $aspectSourceType = GoogleCloudDataplexV1AspectSource::class;
  protected $aspectSourceDataType = '';
  /**
   * Output only. The resource name of the type used to create this Aspect.
   *
   * @var string
   */
  public $aspectType;
  /**
   * Output only. The time when the Aspect was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The content of the aspect, according to its aspect type schema.
   * The maximum size of the field is 120KB (encoded as UTF-8).
   *
   * @var array[]
   */
  public $data;
  /**
   * Output only. The path in the entry under which the aspect is attached.
   *
   * @var string
   */
  public $path;
  /**
   * Output only. The time when the Aspect was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Information related to the source system of the aspect.
   *
   * @param GoogleCloudDataplexV1AspectSource $aspectSource
   */
  public function setAspectSource(GoogleCloudDataplexV1AspectSource $aspectSource)
  {
    $this->aspectSource = $aspectSource;
  }
  /**
   * @return GoogleCloudDataplexV1AspectSource
   */
  public function getAspectSource()
  {
    return $this->aspectSource;
  }
  /**
   * Output only. The resource name of the type used to create this Aspect.
   *
   * @param string $aspectType
   */
  public function setAspectType($aspectType)
  {
    $this->aspectType = $aspectType;
  }
  /**
   * @return string
   */
  public function getAspectType()
  {
    return $this->aspectType;
  }
  /**
   * Output only. The time when the Aspect was created.
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
   * Required. The content of the aspect, according to its aspect type schema.
   * The maximum size of the field is 120KB (encoded as UTF-8).
   *
   * @param array[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return array[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Output only. The path in the entry under which the aspect is attached.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Output only. The time when the Aspect was last updated.
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
class_alias(GoogleCloudDataplexV1Aspect::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Aspect');
