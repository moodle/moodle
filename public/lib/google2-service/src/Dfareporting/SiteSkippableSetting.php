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

class SiteSkippableSetting extends \Google\Model
{
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#siteSkippableSetting".
   *
   * @var string
   */
  public $kind;
  protected $progressOffsetType = VideoOffset::class;
  protected $progressOffsetDataType = '';
  protected $skipOffsetType = VideoOffset::class;
  protected $skipOffsetDataType = '';
  /**
   * Whether the user can skip creatives served to this site. This will act as
   * default for new placements created under this site.
   *
   * @var bool
   */
  public $skippable;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#siteSkippableSetting".
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
   * Amount of time to play videos served to this site template before counting
   * a view. Applicable when skippable is true.
   *
   * @param VideoOffset $progressOffset
   */
  public function setProgressOffset(VideoOffset $progressOffset)
  {
    $this->progressOffset = $progressOffset;
  }
  /**
   * @return VideoOffset
   */
  public function getProgressOffset()
  {
    return $this->progressOffset;
  }
  /**
   * Amount of time to play videos served to this site before the skip button
   * should appear. Applicable when skippable is true.
   *
   * @param VideoOffset $skipOffset
   */
  public function setSkipOffset(VideoOffset $skipOffset)
  {
    $this->skipOffset = $skipOffset;
  }
  /**
   * @return VideoOffset
   */
  public function getSkipOffset()
  {
    return $this->skipOffset;
  }
  /**
   * Whether the user can skip creatives served to this site. This will act as
   * default for new placements created under this site.
   *
   * @param bool $skippable
   */
  public function setSkippable($skippable)
  {
    $this->skippable = $skippable;
  }
  /**
   * @return bool
   */
  public function getSkippable()
  {
    return $this->skippable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SiteSkippableSetting::class, 'Google_Service_Dfareporting_SiteSkippableSetting');
