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

namespace Google\Service\Dfareporting;

class CreativeAssetId extends \Google\Model
{
  public const TYPE_IMAGE = 'IMAGE';
  public const TYPE_FLASH = 'FLASH';
  public const TYPE_VIDEO = 'VIDEO';
  public const TYPE_HTML = 'HTML';
  public const TYPE_HTML_IMAGE = 'HTML_IMAGE';
  public const TYPE_AUDIO = 'AUDIO';
  /**
   * Name of the creative asset. This is a required field while inserting an
   * asset. After insertion, this assetIdentifier is used to identify the
   * uploaded asset. Characters in the name must be alphanumeric or one of the
   * following: ".-_ ". Spaces are allowed.
   *
   * @var string
   */
  public $name;
  /**
   * Type of asset to upload. This is a required field. FLASH and IMAGE are no
   * longer supported for new uploads. All image assets should use HTML_IMAGE.
   *
   * @var string
   */
  public $type;

  /**
   * Name of the creative asset. This is a required field while inserting an
   * asset. After insertion, this assetIdentifier is used to identify the
   * uploaded asset. Characters in the name must be alphanumeric or one of the
   * following: ".-_ ". Spaces are allowed.
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
   * Type of asset to upload. This is a required field. FLASH and IMAGE are no
   * longer supported for new uploads. All image assets should use HTML_IMAGE.
   *
   * Accepted values: IMAGE, FLASH, VIDEO, HTML, HTML_IMAGE, AUDIO
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeAssetId::class, 'Google_Service_Dfareporting_CreativeAssetId');
