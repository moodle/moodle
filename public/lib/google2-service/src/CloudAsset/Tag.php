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

class Tag extends \Google\Model
{
  /**
   * TagKey namespaced name, in the format of {ORG_ID}/{TAG_KEY_SHORT_NAME}.
   *
   * @var string
   */
  public $tagKey;
  /**
   * TagKey ID, in the format of tagKeys/{TAG_KEY_ID}.
   *
   * @var string
   */
  public $tagKeyId;
  /**
   * TagValue namespaced name, in the format of
   * {ORG_ID}/{TAG_KEY_SHORT_NAME}/{TAG_VALUE_SHORT_NAME}.
   *
   * @var string
   */
  public $tagValue;
  /**
   * TagValue ID, in the format of tagValues/{TAG_VALUE_ID}.
   *
   * @var string
   */
  public $tagValueId;

  /**
   * TagKey namespaced name, in the format of {ORG_ID}/{TAG_KEY_SHORT_NAME}.
   *
   * @param string $tagKey
   */
  public function setTagKey($tagKey)
  {
    $this->tagKey = $tagKey;
  }
  /**
   * @return string
   */
  public function getTagKey()
  {
    return $this->tagKey;
  }
  /**
   * TagKey ID, in the format of tagKeys/{TAG_KEY_ID}.
   *
   * @param string $tagKeyId
   */
  public function setTagKeyId($tagKeyId)
  {
    $this->tagKeyId = $tagKeyId;
  }
  /**
   * @return string
   */
  public function getTagKeyId()
  {
    return $this->tagKeyId;
  }
  /**
   * TagValue namespaced name, in the format of
   * {ORG_ID}/{TAG_KEY_SHORT_NAME}/{TAG_VALUE_SHORT_NAME}.
   *
   * @param string $tagValue
   */
  public function setTagValue($tagValue)
  {
    $this->tagValue = $tagValue;
  }
  /**
   * @return string
   */
  public function getTagValue()
  {
    return $this->tagValue;
  }
  /**
   * TagValue ID, in the format of tagValues/{TAG_VALUE_ID}.
   *
   * @param string $tagValueId
   */
  public function setTagValueId($tagValueId)
  {
    $this->tagValueId = $tagValueId;
  }
  /**
   * @return string
   */
  public function getTagValueId()
  {
    return $this->tagValueId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tag::class, 'Google_Service_CloudAsset_Tag');
