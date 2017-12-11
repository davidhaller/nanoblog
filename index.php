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
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once("config.inc.php");
require_once("database.inc.php");
require_once("core.inc.php");

$config = new Config();
$database = new Database($config);
document_start($config, $database, $_GET["view"], "view");

$name = $config->read("name");
echo "<h1 class=\"blog-name\">$name</h1>\n";

if (isset($_GET["view"])) // View one single article
{
    echo "<input type=\"button\" value=\"Show all\" onclick=\"window.location='".$_SERVER["PHP_SELF"]."'\"></br>\n\n";
    echo $database->view_article($_GET["view"]);
}

elseif (isset($_GET["page"]))
{
    echo $database->view_articles($_GET["page"], 5); // View specific page
}

else
{
    echo $database->view_articles(1, 5); // View first article page
}

echo "\n<input type=\"button\" value=\"Create new article\" onclick=\"window.location='edit.php?action=edit'\"/>";
document_end();

unset($config);
unset($database);
?>
