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

class GooglePrivacyDlpV2KMapEstimationConfig extends \Google\Collection
{
  protected $collection_key = 'quasiIds';
  protected $auxiliaryTablesType = GooglePrivacyDlpV2AuxiliaryTable::class;
  protected $auxiliaryTablesDataType = 'array';
  protected $quasiIdsType = GooglePrivacyDlpV2TaggedField::class;
  protected $quasiIdsDataType = 'array';
  /**
   * ISO 3166-1 alpha-2 region code to use in the statistical modeling. Set if
   * no column is tagged with a region-specific InfoType (like US_ZIP_5) or a
   * region code.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Several auxiliary tables can be used in the analysis. Each custom_tag used
   * to tag a quasi-identifiers column must appear in exactly one column of one
   * auxiliary table.
   *
   * @param GooglePrivacyDlpV2AuxiliaryTable[] $auxiliaryTables
   */
  public function setAuxiliaryTables($auxiliaryTables)
  {
    $this->auxiliaryTables = $auxiliaryTables;
  }
  /**
   * @return GooglePrivacyDlpV2AuxiliaryTable[]
   */
  public function getAuxiliaryTables()
  {
    return $this->auxiliaryTables;
  }
  /**
   * Required. Fields considered to be quasi-identifiers. No two columns can
   * have the same tag.
   *
   * @param GooglePrivacyDlpV2TaggedField[] $quasiIds
   */
  public function setQuasiIds($quasiIds)
  {
    $this->quasiIds = $quasiIds;
  }
  /**
   * @return GooglePrivacyDlpV2TaggedField[]
   */
  public function getQuasiIds()
  {
    return $this->quasiIds;
  }
  /**
   * ISO 3166-1 alpha-2 region code to use in the statistical modeling. Set if
   * no column is tagged with a region-specific InfoType (like US_ZIP_5) or a
   * region code.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2KMapEstimationConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2KMapEstimationConfig');
