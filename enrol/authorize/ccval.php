<?php
/************************************************************************
 *
 * CCVal - Credit Card Validation function.
 *
 * Copyright (c) 1999, 2003 Holotech Enterprises. All rights reserved.
 * You may freely modify and use this function for your own purposes. You
 * may freely distribute it, without modification and with this notice
 * and entire header intact.
 *
 * This function accepts a credit card number and, optionally, a code for
 * a credit card name. If a Name code is specified, the number is checked
 * against card-specific criteria, then validated with the Luhn Mod 10
 * formula. Otherwise it is only checked against the formula. Valid name
 * codes are:
 *
 *    mcd - Master Card
 *    vis - Visa
 *    amx - American Express
 *    dsc - Discover
 *    dnc - Diners Club
 *    jcb - JCB
 *    swi - Switch
 *    dlt - Delta
 *    enr - EnRoute
 *
 * You can also optionally specify an expiration date in the formay mmyy.
 * If the validation fails on the date, the function returns 0. If it
 * fails on the number validation, it returns false.
 *
 * A description of the criteria used in this function can be found at
 * http://www.paylib.net/ccval.html. If you have any questions or
 * comments, please direct them to ccval@holotech.net
 *
 *                                          Alan Little
 *                                          Holotech Enterprises
 *                                          http://www.holotech.net/
 *                                          August 2003
 *
 ************************************************************************/

  function CCVal($Num, $Name = "n/a", $Exp = "") {

//  Check the expiration date first
    if (strlen($Exp)) {
      $Month = substr($Exp, 0, 2);
      $Year  = substr($Exp, -2);

      $WorkDate = "$Month/01/$Year";
      $WorkDate = strtotime($WorkDate);
      $LastDay  = date("t", $WorkDate);

      $Expires  = strtotime("$Month/$LastDay/$Year 11:59:59");
      if ($Expires < time()) return 0;
    }

//  Innocent until proven guilty
    $GoodCard = true;

//  Get rid of any non-digits
    $Num = ereg_replace("[^0-9]", "", $Num);

//  Perform card-specific checks, if applicable
    switch ($Name) {

    case "mcd" :
      $GoodCard = ereg("^5[1-5].{14}$", $Num);
      break;

    case "vis" :
      $GoodCard = ereg("^4.{15}$|^4.{12}$", $Num);
      break;

    case "amx" :
      $GoodCard = ereg("^3[47].{13}$", $Num);
      break;

    case "dsc" :
      $GoodCard = ereg("^6011.{12}$", $Num);
      break;

    case "dnc" :
      $GoodCard = ereg("^30[0-5].{11}$|^3[68].{12}$", $Num);
      break;

    case "jcb" :
      $GoodCard = ereg("^3.{15}$|^2131|1800.{11}$", $Num);
      break;
  
    case "dlt" :
      $GoodCard = ereg("^4.{15}$", $Num);
      break;

    case "swi" :
      $GoodCard = ereg("^[456].{15}$|^[456].{17,18}$", $Num);
      break;

    case "enr" :
      $GoodCard = ereg("^2014.{11}$|^2149.{11}$", $Num);
      break;
    }

//  The Luhn formula works right to left, so reverse the number.
    $Num = strrev($Num);

    $Total = 0;

    for ($x=0; $x<strlen($Num); $x++) {
      $digit = substr($Num,$x,1);

//    If it's an odd digit, double it
      if ($x/2 != floor($x/2)) {
        $digit *= 2;

//    If the result is two digits, add them
        if (strlen($digit) == 2)
          $digit = substr($digit,0,1) + substr($digit,1,1);
      }

//    Add the current digit, doubled and added if applicable, to the Total
      $Total += $digit;
    }

//  If it passed (or bypassed) the card-specific check and the Total is
//  evenly divisible by 10, it's cool!
    if ($GoodCard && $Total % 10 == 0) return true; else return false;
  }
?>