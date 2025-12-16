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

namespace Google\Service\BigtableAdmin;

class CheckConsistencyRequest extends \Google\Model
{
  /**
   * Required. The token created using GenerateConsistencyToken for the Table.
   *
   * @var string
   */
  public $consistencyToken;
  protected $dataBoostReadLocalWritesType = DataBoostReadLocalWrites::class;
  protected $dataBoostReadLocalWritesDataType = '';
  protected $standardReadRemoteWritesType = StandardReadRemoteWrites::class;
  protected $standardReadRemoteWritesDataType = '';

  /**
   * Required. The token created using GenerateConsistencyToken for the Table.
   *
   * @param string $consistencyToken
   */
  public function setConsistencyToken($consistencyToken)
  {
    $this->consistencyToken = $consistencyToken;
  }
  /**
   * @return string
   */
  public function getConsistencyToken()
  {
    return $this->consistencyToken;
  }
  /**
   * Checks that reads using an app profile with `DataBoostIsolationReadOnly`
   * can see all writes committed before the token was created, but only if the
   * read and write target the same cluster.
   *
   * @param DataBoostReadLocalWrites $dataBoostReadLocalWrites
   */
  public function setDataBoostReadLocalWrites(DataBoostReadLocalWrites $dataBoostReadLocalWrites)
  {
    $this->dataBoostReadLocalWrites = $dataBoostReadLocalWrites;
  }
  /**
   * @return DataBoostReadLocalWrites
   */
  public function getDataBoostReadLocalWrites()
  {
    return $this->dataBoostReadLocalWrites;
  }
  /**
   * Checks that reads using an app profile with `StandardIsolation` can see all
   * writes committed before the token was created, even if the read and write
   * target different clusters.
   *
   * @param StandardReadRemoteWrites $standardReadRemoteWrites
   */
  public function setStandardReadRemoteWrites(StandardReadRemoteWrites $standardReadRemoteWrites)
  {
    $this->standardReadRemoteWrites = $standardReadRemoteWrites;
  }
  /**
   * @return StandardReadRemoteWrites
   */
  public function getStandardReadRemoteWrites()
  {
    return $this->standardReadRemoteWrites;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckConsistencyRequest::class, 'Google_Service_BigtableAdmin_CheckConsistencyRequest');
