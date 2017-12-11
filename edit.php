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
document_start($config, $database, $_GET["uid"], $_GET["action"]);
document_login($config);

// For creating new articles or editing existing articles
if (isset($_GET["action"]) && $_GET["action"] == "edit")
{
    if (isset($_POST["submit"]))
    {
        if (isset($_GET["uid"]))
        {
            $error = $database->edit_article($_GET["uid"], $_POST["author"], $_POST["title"], $_POST["text"]);

            if ($error)
            {
                echo "<p class=\"error-message\">Editing of article failed.</p>";
            }

            else
            {
                echo "<p class=\"success-message\">Editing successful.</p>";
            }
        }

        else
        {
            $error = $database->add_article($_POST["author"], $_POST["title"], $_POST["text"]);

            if ($error)
            {
                echo "<p class=\"error-message\">Creation of article failed.</p>";
            }

            else
            {
                echo "<p class=\"success-message\">New article $test successfully created.</p>";
            }
        }

        unset($_POST["submit"]);
    }

    else
    {
        $error = false;
        $headline = "Create new article";
        $author = "";
        $title = "";
        $text = "";

        if (isset($_GET["uid"]))
        {
            $error = !$database->has_article($_GET["uid"]);

            if ($error)
            {
                echo "<p class=\"error-message\">Article does not exist.</p>";
            }

            else
            {
                $headline = "Edit article";
                $uid = $database->read($_GET["uid"], "uid");
                $author = $database->read($_GET["uid"], "author");
                $title = $database->read($_GET["uid"], "title");
                $text = $database->read($_GET["uid"], "text");
            }
        }

        if (!$error)
        {

            echo <<< EDITFORM
<br/><br/>\n
<form action="" method="post">\n
<fieldset>\n
<legend>$headline:</legend>\n
<label for="author"">Author:</label><input type="text" name="author" value="$author"/>\n
<label for="title">Title:</label><input type="text" name="title" value="$title"/>\n
<textarea rows="15" cols="85" name="text">$text</textarea>\n
<input type="submit" name="submit" value="Save"/>\n
</fieldset>\n
</form>\n
EDITFORM;
        }
    }
}

// For deleting articles
elseif (isset($_GET["action"]) && $_GET["action"] == "delete")
{
    if (isset($_POST["submit"]))
    {
        $error = $database->remove_article($_GET["uid"]);

        if ($error)
        {
            echo "<p class=\"error-message\">Removal of article failed.</p>";
        }

        else
        {
            echo "<p class=\"success-message\">Article removed.</p>";
        }

        unset($_POST["submit"]);
    }

    elseif (isset($_GET["uid"]))
    {
        if ($database->has_article($_GET["uid"]))
        {
            $title = $database->read($_GET["uid"], "title");
            echo <<< DELETEFORM
<br/><br/>\n
<form action="" method="post">\n
<fieldset>\n
<legend>Article "$title" will be deleted.</legend>\n
<input type="submit" name="submit" value="Delete"/>\n
</fieldset>\n
</form>\n
DELETEFORM;
        }

        else
        {
            echo "<p class=\"error-message\">Article does not exist.</p>";
        }

    }
}

document_end();
unset($config);
unset($database);
?>
