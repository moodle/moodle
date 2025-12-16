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

namespace Google\Service\DisplayVideo;

class AudienceGroupAssignedTargetingOptionDetails extends \Google\Collection
{
  protected $collection_key = 'includedFirstPartyAndPartnerAudienceGroups';
  protected $excludedFirstPartyAndPartnerAudienceGroupType = FirstPartyAndPartnerAudienceGroup::class;
  protected $excludedFirstPartyAndPartnerAudienceGroupDataType = '';
  protected $excludedGoogleAudienceGroupType = GoogleAudienceGroup::class;
  protected $excludedGoogleAudienceGroupDataType = '';
  protected $includedCombinedAudienceGroupType = CombinedAudienceGroup::class;
  protected $includedCombinedAudienceGroupDataType = '';
  protected $includedCustomListGroupType = CustomListGroup::class;
  protected $includedCustomListGroupDataType = '';
  protected $includedFirstPartyAndPartnerAudienceGroupsType = FirstPartyAndPartnerAudienceGroup::class;
  protected $includedFirstPartyAndPartnerAudienceGroupsDataType = 'array';
  protected $includedGoogleAudienceGroupType = GoogleAudienceGroup::class;
  protected $includedGoogleAudienceGroupDataType = '';

  /**
   * Optional. The first party and partner audience ids and recencies of the
   * excluded first party and partner audience group. Used for negative
   * targeting. The COMPLEMENT of the UNION of this group and other excluded
   * audience groups is used as an INTERSECTION to any positive audience
   * targeting. All items are logically ‘OR’ of each other.
   *
   * @param FirstPartyAndPartnerAudienceGroup $excludedFirstPartyAndPartnerAudienceGroup
   */
  public function setExcludedFirstPartyAndPartnerAudienceGroup(FirstPartyAndPartnerAudienceGroup $excludedFirstPartyAndPartnerAudienceGroup)
  {
    $this->excludedFirstPartyAndPartnerAudienceGroup = $excludedFirstPartyAndPartnerAudienceGroup;
  }
  /**
   * @return FirstPartyAndPartnerAudienceGroup
   */
  public function getExcludedFirstPartyAndPartnerAudienceGroup()
  {
    return $this->excludedFirstPartyAndPartnerAudienceGroup;
  }
  /**
   * Optional. The Google audience ids of the excluded Google audience group.
   * Used for negative targeting. The COMPLEMENT of the UNION of this group and
   * other excluded audience groups is used as an INTERSECTION to any positive
   * audience targeting. Only contains Affinity, In-market and Installed-apps
   * type Google audiences. All items are logically ‘OR’ of each other.
   *
   * @param GoogleAudienceGroup $excludedGoogleAudienceGroup
   */
  public function setExcludedGoogleAudienceGroup(GoogleAudienceGroup $excludedGoogleAudienceGroup)
  {
    $this->excludedGoogleAudienceGroup = $excludedGoogleAudienceGroup;
  }
  /**
   * @return GoogleAudienceGroup
   */
  public function getExcludedGoogleAudienceGroup()
  {
    return $this->excludedGoogleAudienceGroup;
  }
  /**
   * Optional. The combined audience ids of the included combined audience
   * group. Contains combined audience ids only.
   *
   * @param CombinedAudienceGroup $includedCombinedAudienceGroup
   */
  public function setIncludedCombinedAudienceGroup(CombinedAudienceGroup $includedCombinedAudienceGroup)
  {
    $this->includedCombinedAudienceGroup = $includedCombinedAudienceGroup;
  }
  /**
   * @return CombinedAudienceGroup
   */
  public function getIncludedCombinedAudienceGroup()
  {
    return $this->includedCombinedAudienceGroup;
  }
  /**
   * Optional. The custom list ids of the included custom list group. Contains
   * custom list ids only.
   *
   * @param CustomListGroup $includedCustomListGroup
   */
  public function setIncludedCustomListGroup(CustomListGroup $includedCustomListGroup)
  {
    $this->includedCustomListGroup = $includedCustomListGroup;
  }
  /**
   * @return CustomListGroup
   */
  public function getIncludedCustomListGroup()
  {
    return $this->includedCustomListGroup;
  }
  /**
   * Optional. The first party and partner audience ids and recencies of
   * included first party and partner audience groups. Each first party and
   * partner audience group contains first party and partner audience ids only.
   * The relation between each first party and partner audience group is
   * INTERSECTION, and the result is UNION'ed with other audience groups.
   * Repeated groups with the same settings will be ignored.
   *
   * @param FirstPartyAndPartnerAudienceGroup[] $includedFirstPartyAndPartnerAudienceGroups
   */
  public function setIncludedFirstPartyAndPartnerAudienceGroups($includedFirstPartyAndPartnerAudienceGroups)
  {
    $this->includedFirstPartyAndPartnerAudienceGroups = $includedFirstPartyAndPartnerAudienceGroups;
  }
  /**
   * @return FirstPartyAndPartnerAudienceGroup[]
   */
  public function getIncludedFirstPartyAndPartnerAudienceGroups()
  {
    return $this->includedFirstPartyAndPartnerAudienceGroups;
  }
  /**
   * Optional. The Google audience ids of the included Google audience group.
   * Contains Google audience ids only.
   *
   * @param GoogleAudienceGroup $includedGoogleAudienceGroup
   */
  public function setIncludedGoogleAudienceGroup(GoogleAudienceGroup $includedGoogleAudienceGroup)
  {
    $this->includedGoogleAudienceGroup = $includedGoogleAudienceGroup;
  }
  /**
   * @return GoogleAudienceGroup
   */
  public function getIncludedGoogleAudienceGroup()
  {
    return $this->includedGoogleAudienceGroup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AudienceGroupAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_AudienceGroupAssignedTargetingOptionDetails');
