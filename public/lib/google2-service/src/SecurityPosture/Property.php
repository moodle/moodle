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

namespace Google\Service\SecurityPosture;

class Property extends \Google\Model
{
  /**
   * Required. The name of the custom source property.
   *
   * @var string
   */
  public $name;
  protected $valueExpressionType = Expr::class;
  protected $valueExpressionDataType = '';

  /**
   * Required. The name of the custom source property.
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
   * Optional. The CEL expression for the value of the custom source property.
   * For resource properties, you can return the value of the property or a
   * string enclosed in quotation marks.
   *
   * @param Expr $valueExpression
   */
  public function setValueExpression(Expr $valueExpression)
  {
    $this->valueExpression = $valueExpression;
  }
  /**
   * @return Expr
   */
  public function getValueExpression()
  {
    return $this->valueExpression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Property::class, 'Google_Service_SecurityPosture_Property');
