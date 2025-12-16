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

namespace Google\Service\BinaryAuthorization;

class GkePolicy extends \Google\Collection
{
  protected $collection_key = 'checkSets';
  protected $checkSetsType = CheckSet::class;
  protected $checkSetsDataType = 'array';
  protected $imageAllowlistType = ImageAllowlist::class;
  protected $imageAllowlistDataType = '';

  /**
   * Optional. The `CheckSet` objects to apply, scoped by namespace or namespace
   * and service account. Exactly one `CheckSet` will be evaluated for a given
   * Pod (unless the list is empty, in which case the behavior is "always
   * allow"). If multiple `CheckSet` objects have scopes that match the
   * namespace and service account of the Pod being evaluated, only the
   * `CheckSet` with the MOST SPECIFIC scope will match. `CheckSet` objects must
   * be listed in order of decreasing specificity, i.e. if a scope matches a
   * given service account (which must include the namespace), it must come
   * before a `CheckSet` with a scope matching just that namespace. This
   * property is enforced by server-side validation. The purpose of this
   * restriction is to ensure that if more than one `CheckSet` matches a given
   * Pod, the `CheckSet` that will be evaluated will always be the first in the
   * list to match (because if any other matches, it must be less specific). If
   * `check_sets` is empty, the default behavior is to allow all images. If
   * `check_sets` is non-empty, the last `check_sets` entry must always be a
   * `CheckSet` with no scope set, i.e. a catchall to handle any situation not
   * caught by the preceding `CheckSet` objects.
   *
   * @param CheckSet[] $checkSets
   */
  public function setCheckSets($checkSets)
  {
    $this->checkSets = $checkSets;
  }
  /**
   * @return CheckSet[]
   */
  public function getCheckSets()
  {
    return $this->checkSets;
  }
  /**
   * Optional. Images exempted from this policy. If any of the patterns match
   * the image being evaluated, the rest of the policy will not be evaluated.
   *
   * @param ImageAllowlist $imageAllowlist
   */
  public function setImageAllowlist(ImageAllowlist $imageAllowlist)
  {
    $this->imageAllowlist = $imageAllowlist;
  }
  /**
   * @return ImageAllowlist
   */
  public function getImageAllowlist()
  {
    return $this->imageAllowlist;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkePolicy::class, 'Google_Service_BinaryAuthorization_GkePolicy');
