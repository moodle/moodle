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

namespace Google\Service\Drive;

class DownloadRestrictionsMetadata extends \Google\Model
{
  protected $effectiveDownloadRestrictionWithContextType = DownloadRestriction::class;
  protected $effectiveDownloadRestrictionWithContextDataType = '';
  protected $itemDownloadRestrictionType = DownloadRestriction::class;
  protected $itemDownloadRestrictionDataType = '';

  /**
   * Output only. The effective download restriction applied to this file. This
   * considers all restriction settings and DLP rules.
   *
   * @param DownloadRestriction $effectiveDownloadRestrictionWithContext
   */
  public function setEffectiveDownloadRestrictionWithContext(DownloadRestriction $effectiveDownloadRestrictionWithContext)
  {
    $this->effectiveDownloadRestrictionWithContext = $effectiveDownloadRestrictionWithContext;
  }
  /**
   * @return DownloadRestriction
   */
  public function getEffectiveDownloadRestrictionWithContext()
  {
    return $this->effectiveDownloadRestrictionWithContext;
  }
  /**
   * The download restriction of the file applied directly by the owner or
   * organizer. This doesn't take into account shared drive settings or DLP
   * rules.
   *
   * @param DownloadRestriction $itemDownloadRestriction
   */
  public function setItemDownloadRestriction(DownloadRestriction $itemDownloadRestriction)
  {
    $this->itemDownloadRestriction = $itemDownloadRestriction;
  }
  /**
   * @return DownloadRestriction
   */
  public function getItemDownloadRestriction()
  {
    return $this->itemDownloadRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DownloadRestrictionsMetadata::class, 'Google_Service_Drive_DownloadRestrictionsMetadata');
