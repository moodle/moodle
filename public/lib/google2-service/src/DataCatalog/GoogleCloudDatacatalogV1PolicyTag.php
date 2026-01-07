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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1PolicyTag extends \Google\Collection
{
  protected $collection_key = 'childPolicyTags';
  /**
   * Output only. Resource names of child policy tags of this policy tag.
   *
   * @var string[]
   */
  public $childPolicyTags;
  /**
   * Description of this policy tag. If not set, defaults to empty. The
   * description must contain only Unicode characters, tabs, newlines, carriage
   * returns and page breaks, and be at most 2000 bytes long when encoded in
   * UTF-8.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User-defined name of this policy tag. The name can't start or end
   * with spaces and must be unique within the parent taxonomy, contain only
   * Unicode letters, numbers, underscores, dashes and spaces, and be at most
   * 200 bytes long when encoded in UTF-8.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. Resource name of this policy tag in the URL format. The policy
   * tag manager generates unique taxonomy IDs and policy tag IDs.
   *
   * @var string
   */
  public $name;
  /**
   * Resource name of this policy tag's parent policy tag. If empty, this is a
   * top level tag. If not set, defaults to an empty string. For example, for
   * the "LatLong" policy tag in the example above, this field contains the
   * resource name of the "Geolocation" policy tag, and, for "Geolocation", this
   * field is empty.
   *
   * @var string
   */
  public $parentPolicyTag;

  /**
   * Output only. Resource names of child policy tags of this policy tag.
   *
   * @param string[] $childPolicyTags
   */
  public function setChildPolicyTags($childPolicyTags)
  {
    $this->childPolicyTags = $childPolicyTags;
  }
  /**
   * @return string[]
   */
  public function getChildPolicyTags()
  {
    return $this->childPolicyTags;
  }
  /**
   * Description of this policy tag. If not set, defaults to empty. The
   * description must contain only Unicode characters, tabs, newlines, carriage
   * returns and page breaks, and be at most 2000 bytes long when encoded in
   * UTF-8.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. User-defined name of this policy tag. The name can't start or end
   * with spaces and must be unique within the parent taxonomy, contain only
   * Unicode letters, numbers, underscores, dashes and spaces, and be at most
   * 200 bytes long when encoded in UTF-8.
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
   * Identifier. Resource name of this policy tag in the URL format. The policy
   * tag manager generates unique taxonomy IDs and policy tag IDs.
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
   * Resource name of this policy tag's parent policy tag. If empty, this is a
   * top level tag. If not set, defaults to an empty string. For example, for
   * the "LatLong" policy tag in the example above, this field contains the
   * resource name of the "Geolocation" policy tag, and, for "Geolocation", this
   * field is empty.
   *
   * @param string $parentPolicyTag
   */
  public function setParentPolicyTag($parentPolicyTag)
  {
    $this->parentPolicyTag = $parentPolicyTag;
  }
  /**
   * @return string
   */
  public function getParentPolicyTag()
  {
    return $this->parentPolicyTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1PolicyTag::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1PolicyTag');
