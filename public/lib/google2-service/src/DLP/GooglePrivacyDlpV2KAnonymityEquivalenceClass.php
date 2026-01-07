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

class GooglePrivacyDlpV2KAnonymityEquivalenceClass extends \Google\Collection
{
  protected $collection_key = 'quasiIdsValues';
  /**
   * Size of the equivalence class, for example number of rows with the above
   * set of values.
   *
   * @var string
   */
  public $equivalenceClassSize;
  protected $quasiIdsValuesType = GooglePrivacyDlpV2Value::class;
  protected $quasiIdsValuesDataType = 'array';

  /**
   * Size of the equivalence class, for example number of rows with the above
   * set of values.
   *
   * @param string $equivalenceClassSize
   */
  public function setEquivalenceClassSize($equivalenceClassSize)
  {
    $this->equivalenceClassSize = $equivalenceClassSize;
  }
  /**
   * @return string
   */
  public function getEquivalenceClassSize()
  {
    return $this->equivalenceClassSize;
  }
  /**
   * Set of values defining the equivalence class. One value per quasi-
   * identifier column in the original KAnonymity metric message. The order is
   * always the same as the original request.
   *
   * @param GooglePrivacyDlpV2Value[] $quasiIdsValues
   */
  public function setQuasiIdsValues($quasiIdsValues)
  {
    $this->quasiIdsValues = $quasiIdsValues;
  }
  /**
   * @return GooglePrivacyDlpV2Value[]
   */
  public function getQuasiIdsValues()
  {
    return $this->quasiIdsValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2KAnonymityEquivalenceClass::class, 'Google_Service_DLP_GooglePrivacyDlpV2KAnonymityEquivalenceClass');
