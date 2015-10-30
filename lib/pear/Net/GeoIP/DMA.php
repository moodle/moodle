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
 * Static class to handle mapping of DMA codes to metro regions.
 * 
 * Use this class with the dmaCode property of the Net_GeoIpLocation object.
 * 
 * <code>
 * $region = Net_GeoIPDMA::getMetroRegion($record->dmaCode);
 * </code>
 * 
 * @category Net
 * @package  Net_GeoIP
 * @author   Hans Lellelid <hans@xmpl.org>
 * @author   Dmitri Snytkine <d.snytkine@gmail.com>
 * @license  LGPL http://www.gnu.org/licenses/lgpl.txt
 * @version  $Revision$
 * @link     http://pear.php.net/package/Net_GeoIp
 */
class Net_GeoIP_DMA
{
    /**
     * Holds DMA -> Metro mapping.
     * @var array
     */
    private static $dmaMap;
    
    /**
     * Initialize
     * 
     * @return void
     */
    public static function initialize()
    {
        self::$dmaMap = array(
            500 => 'Portland-Auburn, ME',
            501 => 'New York, NY',
            502 => 'Binghamton, NY',
            503 => 'Macon, GA',
            504 => 'Philadelphia, PA',
            505 => 'Detroit, MI',
            506 => 'Boston, MA',
            507 => 'Savannah, GA',
            508 => 'Pittsburgh, PA',
            509 => 'Ft Wayne, IN',
            510 => 'Cleveland, OH',
            511 => 'Washington, DC',
            512 => 'Baltimore, MD',
            513 => 'Flint, MI',
            514 => 'Buffalo, NY',
            515 => 'Cincinnati, OH',
            516 => 'Erie, PA',
            517 => 'Charlotte, NC',
            518 => 'Greensboro, NC',
            519 => 'Charleston, SC',
            520 => 'Augusta, GA',
            521 => 'Providence, RI',
            522 => 'Columbus, GA',
            523 => 'Burlington, VT',
            524 => 'Atlanta, GA',
            525 => 'Albany, GA',
            526 => 'Utica-Rome, NY',
            527 => 'Indianapolis, IN',
            528 => 'Miami, FL',
            529 => 'Louisville, KY',
            530 => 'Tallahassee, FL',
            531 => 'Tri-Cities, TN',
            532 => 'Albany-Schenectady-Troy, NY',
            533 => 'Hartford, CT',
            534 => 'Orlando, FL',
            535 => 'Columbus, OH',
            536 => 'Youngstown-Warren, OH',
            537 => 'Bangor, ME',
            538 => 'Rochester, NY',
            539 => 'Tampa, FL',
            540 => 'Traverse City-Cadillac, MI',
            541 => 'Lexington, KY',
            542 => 'Dayton, OH',
            543 => 'Springfield-Holyoke, MA',
            544 => 'Norfolk-Portsmouth, VA',
            545 => 'Greenville-New Bern-Washington, NC',
            546 => 'Columbia, SC',
            547 => 'Toledo, OH',
            548 => 'West Palm Beach, FL',
            549 => 'Watertown, NY',
            550 => 'Wilmington, NC',
            551 => 'Lansing, MI',
            552 => 'Presque Isle, ME',
            553 => 'Marquette, MI',
            554 => 'Wheeling, WV',
            555 => 'Syracuse, NY',
            556 => 'Richmond-Petersburg, VA',
            557 => 'Knoxville, TN',
            558 => 'Lima, OH',
            559 => 'Bluefield-Beckley-Oak Hill, WV',
            560 => 'Raleigh-Durham, NC',
            561 => 'Jacksonville, FL',
            563 => 'Grand Rapids, MI',
            564 => 'Charleston-Huntington, WV',
            565 => 'Elmira, NY',
            566 => 'Harrisburg-Lancaster-Lebanon-York, PA',
            567 => 'Greenville-Spartenburg, SC',
            569 => 'Harrisonburg, VA',
            570 => 'Florence-Myrtle Beach, SC',
            571 => 'Ft Myers, FL',
            573 => 'Roanoke-Lynchburg, VA',
            574 => 'Johnstown-Altoona, PA',
            575 => 'Chattanooga, TN',
            576 => 'Salisbury, MD',
            577 => 'Wilkes Barre-Scranton, PA',
            581 => 'Terre Haute, IN',
            582 => 'Lafayette, IN',
            583 => 'Alpena, MI',
            584 => 'Charlottesville, VA',
            588 => 'South Bend, IN',
            592 => 'Gainesville, FL',
            596 => 'Zanesville, OH',
            597 => 'Parkersburg, WV',
            598 => 'Clarksburg-Weston, WV',
            600 => 'Corpus Christi, TX',
            602 => 'Chicago, IL',
            603 => 'Joplin-Pittsburg, MO',
            604 => 'Columbia-Jefferson City, MO',
            605 => 'Topeka, KS',
            606 => 'Dothan, AL',
            609 => 'St Louis, MO',
            610 => 'Rockford, IL',
            611 => 'Rochester-Mason City-Austin, MN',
            612 => 'Shreveport, LA',
            613 => 'Minneapolis-St Paul, MN',
            616 => 'Kansas City, MO',
            617 => 'Milwaukee, WI',
            618 => 'Houston, TX',
            619 => 'Springfield, MO',
            620 => 'Tuscaloosa, AL',
            622 => 'New Orleans, LA',
            623 => 'Dallas-Fort Worth, TX',
            624 => 'Sioux City, IA',
            625 => 'Waco-Temple-Bryan, TX',
            626 => 'Victoria, TX',
            627 => 'Wichita Falls, TX',
            628 => 'Monroe, LA',
            630 => 'Birmingham, AL',
            631 => 'Ottumwa-Kirksville, IA',
            632 => 'Paducah, KY',
            633 => 'Odessa-Midland, TX',
            634 => 'Amarillo, TX',
            635 => 'Austin, TX',
            636 => 'Harlingen, TX',
            637 => 'Cedar Rapids-Waterloo, IA',
            638 => 'St Joseph, MO',
            639 => 'Jackson, TN',
            640 => 'Memphis, TN',
            641 => 'San Antonio, TX',
            642 => 'Lafayette, LA',
            643 => 'Lake Charles, LA',
            644 => 'Alexandria, LA',
            646 => 'Anniston, AL',
            647 => 'Greenwood-Greenville, MS',
            648 => 'Champaign-Springfield-Decatur, IL',
            649 => 'Evansville, IN',
            650 => 'Oklahoma City, OK',
            651 => 'Lubbock, TX',
            652 => 'Omaha, NE',
            656 => 'Panama City, FL',
            657 => 'Sherman, TX',
            658 => 'Green Bay-Appleton, WI',
            659 => 'Nashville, TN',
            661 => 'San Angelo, TX',
            662 => 'Abilene-Sweetwater, TX',
            669 => 'Madison, WI',
            670 => 'Ft Smith-Fay-Springfield, AR',
            671 => 'Tulsa, OK',
            673 => 'Columbus-Tupelo-West Point, MS',
            675 => 'Peoria-Bloomington, IL',
            676 => 'Duluth, MN',
            678 => 'Wichita, KS',
            679 => 'Des Moines, IA',
            682 => 'Davenport-Rock Island-Moline, IL',
            686 => 'Mobile, AL',
            687 => 'Minot-Bismarck-Dickinson, ND',
            691 => 'Huntsville, AL',
            692 => 'Beaumont-Port Author, TX',
            693 => 'Little Rock-Pine Bluff, AR',
            698 => 'Montgomery, AL',
            702 => 'La Crosse-Eau Claire, WI',
            705 => 'Wausau-Rhinelander, WI',
            709 => 'Tyler-Longview, TX',
            710 => 'Hattiesburg-Laurel, MS',
            711 => 'Meridian, MS',
            716 => 'Baton Rouge, LA',
            717 => 'Quincy, IL',
            718 => 'Jackson, MS',
            722 => 'Lincoln-Hastings, NE',
            724 => 'Fargo-Valley City, ND',
            725 => 'Sioux Falls, SD',
            734 => 'Jonesboro, AR',
            736 => 'Bowling Green, KY',
            737 => 'Mankato, MN',
            740 => 'North Platte, NE',
            743 => 'Anchorage, AK',
            744 => 'Honolulu, HI',
            745 => 'Fairbanks, AK',
            746 => 'Biloxi-Gulfport, MS',
            747 => 'Juneau, AK',
            749 => 'Laredo, TX',
            751 => 'Denver, CO',
            752 => 'Colorado Springs, CO',
            753 => 'Phoenix, AZ',
            754 => 'Butte-Bozeman, MT',
            755 => 'Great Falls, MT',
            756 => 'Billings, MT',
            757 => 'Boise, ID',
            758 => 'Idaho Falls-Pocatello, ID',
            759 => 'Cheyenne, WY',
            760 => 'Twin Falls, ID',
            762 => 'Missoula, MT',
            764 => 'Rapid City, SD',
            765 => 'El Paso, TX',
            766 => 'Helena, MT',
            767 => 'Casper-Riverton, WY',
            770 => 'Salt Lake City, UT',
            771 => 'Yuma, AZ',
            773 => 'Grand Junction, CO',
            789 => 'Tucson, AZ',
            790 => 'Albuquerque, NM',
            798 => 'Glendive, MT',
            800 => 'Bakersfield, CA',
            801 => 'Eugene, OR',
            802 => 'Eureka, CA',
            803 => 'Los Angeles, CA',
            804 => 'Palm Springs, CA',
            807 => 'San Francisco, CA',
            810 => 'Yakima-Pasco, WA',
            811 => 'Reno, NV',
            813 => 'Medford-Klamath Falls, OR',
            819 => 'Seattle-Tacoma, WA',
            820 => 'Portland, OR',
            821 => 'Bend, OR',
            825 => 'San Diego, CA',
            828 => 'Monterey-Salinas, CA',
            839 => 'Las Vegas, NV',
            855 => 'Santa Barbara, CA',
            862 => 'Sacramento, CA',
            866 => 'Fresno, CA',
            868 => 'Chico-Redding, CA',
            881 => 'Spokane, WA');
    }
    
    /**
     * Lookup the metro region based on the provided DMA code.
     * 
     * @param int $dmaCode The DMA code
     * 
     * @return string Metro region name.
     */
    public static function getMetroRegion($dmaCode)
    {
        if ($dmaCode === null) {
            return null;
        }
        if (self::$dmaMap === null) {
            self::initialize();
        }
        return self::$dmaMap[$dmaCode];
    }

    /**
     * Reverse lookup of DMA code if [exact] metro region name is known.
     * 
     * @param string $metro Metro region name.
     * 
     * @return int DMA code, or false if not found.
     */
    public static function getDMACode($metro)    
    {
        if (self::$dmaMap === null) {
            self::initialize();
        }
        return array_search($metro, self::$dmaMap);
    }

}