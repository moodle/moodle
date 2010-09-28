<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP version 5                                                        |
 * +----------------------------------------------------------------------+
 * | Copyright (C) 2004 MaxMind LLC                                       |
 * +----------------------------------------------------------------------+
 * | This library is free software; you can redistribute it and/or        |
 * | modify it under the terms of the GNU Lesser General Public           |
 * | License as published by the Free Software Foundation; either         |
 * | version 2.1 of the License, or (at your option) any later version.   |
 * |                                                                      |
 * | This library is distributed in the hope that it will be useful,      |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
 * | Lesser General Public License for more details.                      |
 * |                                                                      |
 * | You should have received a copy of the GNU Lesser General Public     |
 * | License along with this library; if not, write to the Free Software  |
 * | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 |
 * | USA, or view it online at http://www.gnu.org/licenses/lgpl.txt.      |
 * +----------------------------------------------------------------------+
 * | Authors: Jim Winstead <jimw@apache.org> (original Maxmind version)   |
 * |          Hans Lellelid <hans@xmpl.org>                               |
 * +----------------------------------------------------------------------+
 *
 * @category Net
 * @package  Net_GeoIP
 * @author   Hans Lellelid <hans@xmpl.org>
 * @license  LGPL http://www.gnu.org/licenses/lgpl.txt
 * @link     http://pear.php.net/package/Net_GeoIp
 * $Id$
 */

/**
 * This class represents a location record as returned by Net_GeoIP::lookupLocation().
 *
 * This class is primarily a collection of values (the public properties of the class), but
 * there is also a distance() method to calculate the km distance between two points.
 *
 * @category Net
 * @package  Net_GeoIP
 * @author   Hans Lellelid <hans@xmpl.org>
 * @author   Dmitri Snytkine <d.snytkine@gmail.com>
 * @license  LGPL http://www.gnu.org/licenses/lgpl.txt
 * @version  $Revision$
 * @link     http://pear.php.net/package/Net_GeoIp
 * @see      Net_GeoIP::lookupLocation()
 */
class Net_GeoIP_Location implements Serializable
{
    protected $aData = array(
        'countryCode'  => null,
        'countryCode3' => null,
        'countryName'  => null,
        'region'       => null,
        'city'         => null,
        'postalCode'   => null,
        'latitude'     => null,
        'longitude'    => null,
        'areaCode'     => null,
        'dmaCode'      => null
    );


    /**
     * Calculate the distance in km between two points.
     *
     * @param Net_GeoIP_Location $loc The other point to which distance will be calculated.
     *
     * @return float The number of km between two points on the globe.
     */
    public function distance(Net_GeoIP_Location $loc)
    {
        // ideally these should be class constants, but class constants
        // can't be operations.
        $RAD_CONVERT = M_PI / 180;
        $EARTH_DIAMETER = 2 * 6378.2;

        $lat1 = $this->latitude;
        $lon1 = $this->longitude;
        $lat2 = $loc->latitude;
        $lon2 = $loc->longitude;

        // convert degrees to radians
        $lat1 *= $RAD_CONVERT;
        $lat2 *= $RAD_CONVERT;

        // find the deltas
        $delta_lat = $lat2 - $lat1;
        $delta_lon = ($lon2 - $lon1) * $RAD_CONVERT;

        // Find the great circle distance
        $temp = pow(sin($delta_lat/2), 2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2), 2);
        return $EARTH_DIAMETER * atan2(sqrt($temp), sqrt(1-$temp));
    }

    /**
     * magic method to make it possible
     * to store this object in cache when
     * automatic serialization is on
     * Specifically it makes it possible to store
     * this object in memcache
     *
     * @return array
     */
    public function serialize()
    {
        return serialize($this->aData);
    }

    /**
     * unserialize a representation of the object
     *
     * @param array $serialized The serialized representation of the location
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->aData = unserialize($serialized);
    }


    /**
     * Setter for elements of $this->aData array
     *
     * @param string $name The variable to set
     * @param string $val  The value
     *
     * @return object $this object
     */
    public function set($name, $val)
    {
        if (array_key_exists($name, $this->aData)) {
            $this->aData[$name] = $val;
        }

        return $this;
    }

    public function __set($name, $val)
    {
        return $this->set($name, $val);
    }

    /**
     * Getter for $this->aData array
     *
     * @return array
     */
    public function getData()
    {
         return $this->aData;
    }


    /**
     * Magic method to get value from $this->aData array
     *
     * @param string $name The var to get
     *
     * @return mixed string if value exists or null if it is empty of
     * just does not exist
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->aData)) {
            return $this->aData[$name];
        }

        return null;
    }


    /**
     * String representation of the object
     *
     * @return string text and result of print_r of $this->aData array
     */
    public function __toString()
    {
        return 'object of type '.__CLASS__.'. data: '.implode(',', $this->aData);
    }


    /**
     * Magic method
     * makes it possible to check if specific record exists
     * and also makes it possible to use empty() on any property
     *
     * @param strign $name The name of the var to check
     *
     * @return bool
     */
    public function __isset($name)
    {
        return (null !== $this->__get($name));
    }

}
