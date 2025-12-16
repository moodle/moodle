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

namespace Google\Service\CloudNaturalLanguage;

class XPSSpeechModelSpec extends \Google\Collection
{
  protected $collection_key = 'subModelSpecs';
  /**
   * Required for speech xps backend. Speech xps has to use dataset_id and
   * model_id as the primary key in db so that speech API can query the db
   * directly.
   *
   * @var string
   */
  public $datasetId;
  /**
   * @var string
   */
  public $language;
  protected $subModelSpecsType = XPSSpeechModelSpecSubModelSpec::class;
  protected $subModelSpecsDataType = 'array';

  /**
   * Required for speech xps backend. Speech xps has to use dataset_id and
   * model_id as the primary key in db so that speech API can query the db
   * directly.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Model specs for all submodels contained in this model.
   *
   * @param XPSSpeechModelSpecSubModelSpec[] $subModelSpecs
   */
  public function setSubModelSpecs($subModelSpecs)
  {
    $this->subModelSpecs = $subModelSpecs;
  }
  /**
   * @return XPSSpeechModelSpecSubModelSpec[]
   */
  public function getSubModelSpecs()
  {
    return $this->subModelSpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSSpeechModelSpec::class, 'Google_Service_CloudNaturalLanguage_XPSSpeechModelSpec');
