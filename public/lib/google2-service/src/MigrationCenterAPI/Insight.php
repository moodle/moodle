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

namespace Google\Service\MigrationCenterAPI;

class Insight extends \Google\Model
{
  protected $genericInsightType = GenericInsight::class;
  protected $genericInsightDataType = '';
  protected $migrationInsightType = MigrationInsight::class;
  protected $migrationInsightDataType = '';

  /**
   * Output only. A generic insight about an asset.
   *
   * @param GenericInsight $genericInsight
   */
  public function setGenericInsight(GenericInsight $genericInsight)
  {
    $this->genericInsight = $genericInsight;
  }
  /**
   * @return GenericInsight
   */
  public function getGenericInsight()
  {
    return $this->genericInsight;
  }
  /**
   * Output only. An insight about potential migrations for an asset.
   *
   * @param MigrationInsight $migrationInsight
   */
  public function setMigrationInsight(MigrationInsight $migrationInsight)
  {
    $this->migrationInsight = $migrationInsight;
  }
  /**
   * @return MigrationInsight
   */
  public function getMigrationInsight()
  {
    return $this->migrationInsight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Insight::class, 'Google_Service_MigrationCenterAPI_Insight');
