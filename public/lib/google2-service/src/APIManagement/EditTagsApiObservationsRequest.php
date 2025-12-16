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

namespace Google\Service\APIManagement;

class EditTagsApiObservationsRequest extends \Google\Collection
{
  protected $collection_key = 'tagActions';
  /**
   * Required. Identifier of ApiObservation need to be edit tags Format example:
   * "apigee.googleapis.com|us-west1|443"
   *
   * @var string
   */
  public $apiObservationId;
  protected $tagActionsType = TagAction::class;
  protected $tagActionsDataType = 'array';

  /**
   * Required. Identifier of ApiObservation need to be edit tags Format example:
   * "apigee.googleapis.com|us-west1|443"
   *
   * @param string $apiObservationId
   */
  public function setApiObservationId($apiObservationId)
  {
    $this->apiObservationId = $apiObservationId;
  }
  /**
   * @return string
   */
  public function getApiObservationId()
  {
    return $this->apiObservationId;
  }
  /**
   * Required. Tag actions to be applied
   *
   * @param TagAction[] $tagActions
   */
  public function setTagActions($tagActions)
  {
    $this->tagActions = $tagActions;
  }
  /**
   * @return TagAction[]
   */
  public function getTagActions()
  {
    return $this->tagActions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EditTagsApiObservationsRequest::class, 'Google_Service_APIManagement_EditTagsApiObservationsRequest');
