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

namespace Google\Service\CloudAsset;

class EffectiveTagDetails extends \Google\Collection
{
  protected $collection_key = 'effectiveTags';
  /**
   * The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the ancestor from which
   * effective_tags are inherited, according to [tag
   * inheritance](https://cloud.google.com/resource-manager/docs/tags/tags-
   * overview#inheritance).
   *
   * @var string
   */
  public $attachedResource;
  protected $effectiveTagsType = Tag::class;
  protected $effectiveTagsDataType = 'array';

  /**
   * The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the ancestor from which
   * effective_tags are inherited, according to [tag
   * inheritance](https://cloud.google.com/resource-manager/docs/tags/tags-
   * overview#inheritance).
   *
   * @param string $attachedResource
   */
  public function setAttachedResource($attachedResource)
  {
    $this->attachedResource = $attachedResource;
  }
  /**
   * @return string
   */
  public function getAttachedResource()
  {
    return $this->attachedResource;
  }
  /**
   * The effective tags inherited from the attached_resource. Note that tags
   * with the same key but different values may attach to resources at a
   * different hierarchy levels. The lower hierarchy tag value will overwrite
   * the higher hierarchy tag value of the same tag key. In this case, the tag
   * value at the higher hierarchy level will be removed. For more information,
   * see [tag inheritance](https://cloud.google.com/resource-
   * manager/docs/tags/tags-overview#inheritance).
   *
   * @param Tag[] $effectiveTags
   */
  public function setEffectiveTags($effectiveTags)
  {
    $this->effectiveTags = $effectiveTags;
  }
  /**
   * @return Tag[]
   */
  public function getEffectiveTags()
  {
    return $this->effectiveTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EffectiveTagDetails::class, 'Google_Service_CloudAsset_EffectiveTagDetails');
