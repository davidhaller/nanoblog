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

session_start();

function document_start($config, $database, $uid, $action)
{
    // Generate title, depending on context

    $title = $config->read("name");

    if (isset($uid) && $database->has_article($uid))
    {
        $title = $database->read($uid, "title");

        if ($action == "edit")
        {
            $title = "Edit \"".$title."\"";
        }

        if ($action == "delete")
        {
            $title = "Delete \"".$title."\"";
        }
    }

    elseif ($action == "edit")
    {
        $title = "Create new article";
    }

    $header = <<< HEADER
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title>$title</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
HEADER;

    // Do logout when clicked

    if (isset($_GET["action"]) && $_GET["action"] == "logout")
    {
        session_destroy();
        echo <<< BACK
<script type="text/javascript">
history.back();
</script>
BACK;
    }

    // Generate logout button

    elseif (isset($_SESSION["login"]) && $_SESSION["login"])
    {
        $header.= "\n<input type=\"button\" class=\"logout-button\" value=\"Logout\" onclick=\"window.location='?action=logout'\">";
    }

    echo $header;
}

function document_end()
{
    $foot = <<< FOOT
</body>
</html>
FOOT;

    echo $foot;
}

function document_login($config)
{
    echo "<input type=\"button\" class=\"back-button\" value=\"Back to articles\" onclick=\"window.location='index.php'\"/>";

    if (isset($_POST["user"]) && isset($_POST["password"]))
    {
        if ($_POST["user"] == $config->read("user") && $_POST["password"] == $config->read("password"))
        {
            session_regenerate_id(true); // Avoid Session Hijacking
            $_SESSION["login"] = true;

            unset($_POST["user"]);
            unset($_POST["password"]);
        }

        else
        {
            echo "<p class=\"error-message\">Username or password are incorrect.</p>";
        }

    }

    if (!isset($_SESSION["login"]) || !$_SESSION["login"])
    {
        echo <<< LOGINFORM
<br/><br/>
<form action="" method="post">
<fieldset>
<legend>Login</legend>
<label for="user">Username:</label> <input type="text" name="user"/>
<label for="password"> Password:</label> <input type="password" name="password"/>
<input type="submit" value="Login"/>
</fieldset>
</form>
LOGINFORM;
        document_end();
        exit;
    }
}
?>
