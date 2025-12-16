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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2ExcludeInfoTypes extends \Google\Collection
{
  protected $collection_key = 'infoTypes';
  protected $infoTypesType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypesDataType = 'array';

  /**
   * InfoType list in ExclusionRule rule drops a finding when it overlaps or
   * contained within with a finding of an infoType from this list. For example,
   * for `InspectionRuleSet.info_types` containing "PHONE_NUMBER"` and
   * `exclusion_rule` containing `exclude_info_types.info_types` with
   * "EMAIL_ADDRESS" the phone number findings are dropped if they overlap with
   * EMAIL_ADDRESS finding. That leads to "555-222-2222@example.org" to generate
   * only a single finding, namely email address.
   *
   * @param GooglePrivacyDlpV2InfoType[] $infoTypes
   */
  public function setInfoTypes($infoTypes)
  {
    $this->infoTypes = $infoTypes;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType[]
   */
  public function getInfoTypes()
  {
    return $this->infoTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ExcludeInfoTypes::class, 'Google_Service_DLP_GooglePrivacyDlpV2ExcludeInfoTypes');
