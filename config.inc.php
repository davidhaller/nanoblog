<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

class Config
{
    private $handle; // Link to the XML file

    function __construct()
    {
        $this->handle = new DOMDocument("1.0", "UTF-8");
        $this->handle->load("config.xml") or die("Configuration file not found.");
    }

    function read($key)
    {
        return $this->handle->getElementsByTagName($key)->item(0)->nodeValue;
    }
}
?>
