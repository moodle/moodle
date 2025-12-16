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

namespace Google\Service\Datalineage;

class GoogleCloudDatacatalogLineageV1Process extends \Google\Model
{
  /**
   * Optional. The attributes of the process. Should only be used for the
   * purpose of non-semantic management (classifying, describing or labeling the
   * process). Up to 100 attributes are allowed.
   *
   * @var array[]
   */
  public $attributes;
  /**
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The resource name of the lineage process. Format:
   * `projects/{project}/locations/{location}/processes/{process}`. Can be
   * specified or auto-assigned. {process} must be not longer than 200
   * characters and only contain characters in a set: `a-zA-Z0-9_-:.`
   *
   * @var string
   */
  public $name;
  protected $originType = GoogleCloudDatacatalogLineageV1Origin::class;
  protected $originDataType = '';

  /**
   * Optional. The attributes of the process. Should only be used for the
   * purpose of non-semantic management (classifying, describing or labeling the
   * process). Up to 100 attributes are allowed.
   *
   * @param array[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return array[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
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
   * Immutable. The resource name of the lineage process. Format:
   * `projects/{project}/locations/{location}/processes/{process}`. Can be
   * specified or auto-assigned. {process} must be not longer than 200
   * characters and only contain characters in a set: `a-zA-Z0-9_-:.`
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
   * Optional. The origin of this process and its runs and lineage events.
   *
   * @param GoogleCloudDatacatalogLineageV1Origin $origin
   */
  public function setOrigin(GoogleCloudDatacatalogLineageV1Origin $origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1Origin
   */
  public function getOrigin()
  {
    return $this->origin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1Process::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1Process');
