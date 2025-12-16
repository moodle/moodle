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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1QaScorecard extends \Google\Model
{
  /**
   * The source of the scorecard is unspecified. Default to
   * QA_SCORECARD_SOURCE_CUSTOMER_DEFINED.
   */
  public const SOURCE_QA_SCORECARD_SOURCE_UNSPECIFIED = 'QA_SCORECARD_SOURCE_UNSPECIFIED';
  /**
   * The scorecard is a custom scorecard created by the user.
   */
  public const SOURCE_QA_SCORECARD_SOURCE_CUSTOMER_DEFINED = 'QA_SCORECARD_SOURCE_CUSTOMER_DEFINED';
  /**
   * The scorecard is a scorecard created through discovery engine deployment.
   */
  public const SOURCE_QA_SCORECARD_SOURCE_DISCOVERY_ENGINE = 'QA_SCORECARD_SOURCE_DISCOVERY_ENGINE';
  /**
   * Output only. The time at which this scorecard was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A text description explaining the intent of the scorecard.
   *
   * @var string
   */
  public $description;
  /**
   * The user-specified display name of the scorecard.
   *
   * @var string
   */
  public $displayName;
  /**
   * Whether the scorecard is the default one for the project. A default
   * scorecard cannot be deleted and will always appear first in scorecard
   * selector.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Identifier. The scorecard name. Format:
   * projects/{project}/locations/{location}/qaScorecards/{qa_scorecard}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The source of the scorecard.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. The most recent time at which the scorecard was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which this scorecard was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * A text description explaining the intent of the scorecard.
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
   * The user-specified display name of the scorecard.
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
   * Whether the scorecard is the default one for the project. A default
   * scorecard cannot be deleted and will always appear first in scorecard
   * selector.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Identifier. The scorecard name. Format:
   * projects/{project}/locations/{location}/qaScorecards/{qa_scorecard}
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
   * Output only. The source of the scorecard.
   *
   * Accepted values: QA_SCORECARD_SOURCE_UNSPECIFIED,
   * QA_SCORECARD_SOURCE_CUSTOMER_DEFINED, QA_SCORECARD_SOURCE_DISCOVERY_ENGINE
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. The most recent time at which the scorecard was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1QaScorecard::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaScorecard');
