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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SetDefaultBranchRequest extends \Google\Model
{
  /**
   * The final component of the resource name of a branch. This field must be
   * one of "0", "1" or "2". Otherwise, an INVALID_ARGUMENT error is returned.
   * If there are no sufficient active products in the targeted branch and force
   * is not set, a FAILED_PRECONDITION error is returned.
   *
   * @var string
   */
  public $branchId;
  /**
   * If set to true, it permits switching to a branch with branch_id even if it
   * has no sufficient active products.
   *
   * @var bool
   */
  public $force;
  /**
   * Some note on this request, this can be retrieved by
   * CatalogService.GetDefaultBranch before next valid default branch set
   * occurs. This field must be a UTF-8 encoded string with a length limit of
   * 1,000 characters. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $note;

  /**
   * The final component of the resource name of a branch. This field must be
   * one of "0", "1" or "2". Otherwise, an INVALID_ARGUMENT error is returned.
   * If there are no sufficient active products in the targeted branch and force
   * is not set, a FAILED_PRECONDITION error is returned.
   *
   * @param string $branchId
   */
  public function setBranchId($branchId)
  {
    $this->branchId = $branchId;
  }
  /**
   * @return string
   */
  public function getBranchId()
  {
    return $this->branchId;
  }
  /**
   * If set to true, it permits switching to a branch with branch_id even if it
   * has no sufficient active products.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Some note on this request, this can be retrieved by
   * CatalogService.GetDefaultBranch before next valid default branch set
   * occurs. This field must be a UTF-8 encoded string with a length limit of
   * 1,000 characters. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SetDefaultBranchRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SetDefaultBranchRequest');
