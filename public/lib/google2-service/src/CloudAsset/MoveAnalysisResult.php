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

namespace Google\Service\CloudAsset;

class MoveAnalysisResult extends \Google\Collection
{
  protected $collection_key = 'warnings';
  protected $blockersType = MoveImpact::class;
  protected $blockersDataType = 'array';
  protected $warningsType = MoveImpact::class;
  protected $warningsDataType = 'array';

  /**
   * Blocking information that would prevent the target resource from moving to
   * the specified destination at runtime.
   *
   * @param MoveImpact[] $blockers
   */
  public function setBlockers($blockers)
  {
    $this->blockers = $blockers;
  }
  /**
   * @return MoveImpact[]
   */
  public function getBlockers()
  {
    return $this->blockers;
  }
  /**
   * Warning information indicating that moving the target resource to the
   * specified destination might be unsafe. This can include important policy
   * information and configuration changes, but will not block moves at runtime.
   *
   * @param MoveImpact[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return MoveImpact[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoveAnalysisResult::class, 'Google_Service_CloudAsset_MoveAnalysisResult');
