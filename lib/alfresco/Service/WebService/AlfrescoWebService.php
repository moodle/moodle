<?php

/*
 * Copyright (C) 2005-2010 Alfresco Software Limited.
 *
 * This file is part of Alfresco
 *
 * Alfresco is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Alfresco is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Alfresco. If not, see <http://www.gnu.org/licenses/>.
 */

class AlfrescoWebService extends SoapClient
{
   private $securityExtNS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
   private $wsUtilityNS   = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd";
   private $passwordType  = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText";

   private $ticket;
   
   public function __construct($wsdl, $options = array('trace' => true, 'exceptions' => true), $ticket = null)
   {
      // Store the current ticket
      $this->ticket = $ticket;

      // Call the base class
      parent::__construct($wsdl, $options);
   }

   public function __call($function_name, $arguments=array())
   {
      return $this->__soapCall($function_name, $arguments);
   }

   public function __soapCall($function_name, $arguments=array(), $options=array(), $input_headers=array(), &$output_headers=array())
   {
      if (isset($this->ticket))
      {
         // Automatically add a security header         
         $input_headers[] = new SoapHeader($this->securityExtNS, "Security", null, 1);
         
         // Set the JSESSION cookie value
         $sessionId = Alfresco_Repository::getSessionId($this->ticket);
         if ($sessionId != null)
         {
         	$this->__setCookie("JSESSIONID", $sessionId);
         }
      }
      
      return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);   
   }
   
   public function __doRequest($request, $location, $action, $version, $one_way = 0)
   {
      // If this request requires authentication we have to manually construct the
      // security headers.
      if (isset($this->ticket))
      { 
         $dom = new DOMDocument("1.0");
         $dom->loadXML($request);

         $securityHeader = $dom->getElementsByTagName("Security");

         if ($securityHeader->length != 1)
         {
            throw new Exception("Expected length: 1, Received: " . $securityHeader->length . ". No Security Header, or more than one element called Security!");
         }
      
         $securityHeader = $securityHeader->item(0);

         // Construct Timestamp Header
         $timeStamp = $dom->createElementNS($this->wsUtilityNS, "Timestamp");
         $createdDate = date("Y-m-d\TH:i:s\Z", mktime(date("H")+24, date("i"), date("s"), date("m"), date("d"), date("Y")));
         $expiresDate = date("Y-m-d\TH:i:s\Z", mktime(date("H")+25, date("i"), date("s"), date("m"), date("d"), date("Y")));
         $created = new DOMElement("Created", $createdDate, $this->wsUtilityNS);
         $expires = new DOMElement("Expires", $expiresDate, $this->wsUtilityNS);
         $timeStamp->appendChild($created);
         $timeStamp->appendChild($expires);

         // Construct UsernameToken Header
         $userNameToken = $dom->createElementNS($this->securityExtNS, "UsernameToken");
         $userName = new DOMElement("Username", "username", $this->securityExtNS);
         $passWord = $dom->createElementNS($this->securityExtNS, "Password");
         $typeAttr = new DOMAttr("Type", $this->passwordType);
         $passWord->appendChild($typeAttr);
         $passWord->appendChild($dom->createTextNode($this->ticket));
         $userNameToken->appendChild($userName);
         $userNameToken->appendChild($passWord);

         // Construct Security Header
         $securityHeader->appendChild($timeStamp);
         $securityHeader->appendChild($userNameToken);

         // Save the XML Request
         $request = $dom->saveXML();
      }

      return parent::__doRequest($request, $location, $action, $version);
   }
}

?>
