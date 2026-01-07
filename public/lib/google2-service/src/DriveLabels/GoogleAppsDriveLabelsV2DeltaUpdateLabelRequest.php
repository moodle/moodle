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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2DeltaUpdateLabelRequest extends \Google\Collection
{
  /**
   * Implies the field mask: `name,id,revision_id,label_type,properties.*`
   */
  public const VIEW_LABEL_VIEW_BASIC = 'LABEL_VIEW_BASIC';
  /**
   * All possible fields.
   */
  public const VIEW_LABEL_VIEW_FULL = 'LABEL_VIEW_FULL';
  protected $collection_key = 'requests';
  /**
   * The BCP-47 language code to use for evaluating localized field labels when
   * `include_label_in_response` is `true`.
   *
   * @var string
   */
  public $languageCode;
  protected $requestsType = GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestRequest::class;
  protected $requestsDataType = 'array';
  /**
   * Set to `true` in order to use the user's admin credentials. The server will
   * verify the user is an admin for the label before allowing access.
   *
   * @var bool
   */
  public $useAdminAccess;
  /**
   * When specified, only certain fields belonging to the indicated view will be
   * returned.
   *
   * @var string
   */
  public $view;
  protected $writeControlType = GoogleAppsDriveLabelsV2WriteControl::class;
  protected $writeControlDataType = '';

  /**
   * The BCP-47 language code to use for evaluating localized field labels when
   * `include_label_in_response` is `true`.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * A list of updates to apply to the label. Requests will be applied in the
   * order they are specified.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestRequest[] $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestRequest[]
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * Set to `true` in order to use the user's admin credentials. The server will
   * verify the user is an admin for the label before allowing access.
   *
   * @param bool $useAdminAccess
   */
  public function setUseAdminAccess($useAdminAccess)
  {
    $this->useAdminAccess = $useAdminAccess;
  }
  /**
   * @return bool
   */
  public function getUseAdminAccess()
  {
    return $this->useAdminAccess;
  }
  /**
   * When specified, only certain fields belonging to the indicated view will be
   * returned.
   *
   * Accepted values: LABEL_VIEW_BASIC, LABEL_VIEW_FULL
   *
   * @param self::VIEW_* $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return self::VIEW_*
   */
  public function getView()
  {
    return $this->view;
  }
  /**
   * Provides control over how write requests are executed.
   *
   * @param GoogleAppsDriveLabelsV2WriteControl $writeControl
   */
  public function setWriteControl(GoogleAppsDriveLabelsV2WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  /**
   * @return GoogleAppsDriveLabelsV2WriteControl
   */
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2DeltaUpdateLabelRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2DeltaUpdateLabelRequest');
