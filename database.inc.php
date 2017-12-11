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

class Database
{
    private $handle; // Link to the PostgreSQL database

    function __construct($config)
    {
        $database = $config->read("database");
        $host = $config->read("host");
        $user = $config->read("user");
        $password = $config->read("password");

        $this->handle = pg_connect("dbname=$database host=$host user=$user password=$password");

        if ($this->handle == false)
            die("Connection to database failed.");
    }

    function __destruct()
    {
        pg_close($this->handle);
    }

    /**
     * Called by view_article to convert its SQL query to XHTML.
     */

    private function generate($result)
    {
        if (pg_num_rows($result) > 0)
        {
            $output = "";
            $url = $_SERVER["PHP_SELF"];

            // Go through all found articles and generate XHTML for each

            while ($dataset = pg_fetch_assoc($result))
            {
                $uid = $dataset["uid"];
                $author = $dataset["author"];
                $title = $dataset["title"];

                $timestamp = new DateTime($dataset["timestamp"]);
                $date = $timestamp->format("j.m.Y");
                $time = $timestamp->format("H:i:s");

                $text = nl2br($dataset["text"]); // Needed for correct displaying of linebreaks

                $output .= <<< ARTICLE
<h2 class="article-title">$title</h2>\n
<p class="article-info"><em>Written by $author on $date at <a style="color: inherit" href="$url?view=$uid">$time</a></em>
<input class="article-action" type="button" value="Delete" onclick="window.location='edit.php?action=delete&uid=$uid'"/>
<input class="article-action" type="button" value="Edit" onclick="window.location='edit.php?action=edit&uid=$uid'"/>
</p>\n
<p class="article-text">$text\n</p>\n\n
ARTICLE;
            }

            return $output;
        }

        else
        {
            return null;
        }
    }

    /**
     * Returns an article as XHTML.
     */

    function view_article($uid)
    {
        pg_prepare($this->handle, "view_article", "select * from articles where uid = $1;");
        $result = pg_execute($this->handle, "view_article", [$uid]);

        $output = $this->generate($result);

        if ($output == null)
        {
            return "<p class=\"error-message\">Article does not exist.</p>";
        }

        else
        {
            return $output;
        }
    }

    /**
     * Returns articles on a specific page as XHTML in reverse chronological order.
     */

    function view_articles($page, $step)
    {
        $start = ($page * $step) - $step;

        pg_prepare($this->handle, "view_articles", "select * from articles order by timestamp desc limit $1 offset $2;");
        $result = pg_execute($this->handle, "view_articles", [$step, $start]);

        $output = $this->generate($result);

        if ($output == null)
        {
            return "<p class=\"error-message\">No articles available.</p>";
        }

        else
        {
            // Generate previous/next page links

            $url = $_SERVER["PHP_SELF"];

            $navigation = "";

            if ($page > 1)
            {
                $prevpage = $page - 1;
                $navigation .= "<a class=\"page-previous\" href=\"$url?page=$prevpage\"> << Previous </a>";
            }

            $rows = pg_num_rows(pg_query($this->handle, "select uid from articles;"));
            $pages = $rows / $step;

            if ($page < $pages)
            {
                $nextpage = $page + 1;
                $navigation .= "<a class=\"page-next\" href=\"$url?page=$nextpage\"> Next >> </a>";
            }

            if ($navigation != "")
            {
                $output .= $navigation."<br/><br/>\n";
            }

            return $output;
        }
    }

    /**
     * Adds an new article consisting of $author, $title and $text.
     */

    function add_article($author, $title, $text)
    {
        pg_prepare($this->handle, "add_article", "insert into articles(author, title, text) values($1, $2, $3);");
        $result = pg_execute($this->handle, "add_article", [$author, $title, $text]);

        if (pg_affected_rows($result) != 1)
            return -1;

        else return 0;
    }

    /**
     * Changes the author, title and the text of an article specified by its $uid
     * to $newauthor, $newtitle and $newtext.
     */

    function edit_article($uid, $newauthor, $newtitle, $newtext)
    {
        pg_prepare($this->handle, "edit_article", "update articles set author = $1, title = $2, text = $3, timestamp = current_timestamp where uid = $4;");
        $result = pg_execute($this->handle, "edit_article", [$newauthor, $newtitle, $newtext, $uid]);

        if (pg_affected_rows($result) != 1)
            return -1;

        else return 0;
    }

    /**
     * Removes an article specified by its $uid.
     */

    function remove_article($uid)
    {
        pg_prepare($this->handle, "remove_article", "delete from articles where uid = $1;");
        $result = pg_execute($this->handle, "remove_article", [$uid]);

        if (pg_affected_rows($result) != 1)
            return -1;

        else return 0;
    }

    /**
     * Checks if an article with $uid exists.
     */

    function has_article($uid)
    {
        pg_prepare($this->handle, "has_article", "select uid from articles where uid = $1;");
        $result = pg_execute($this->handle, "has_article", [$uid]);

        if (pg_num_rows($result) > 0)
            return true;

        else return false;
    }

    /**
     * Returns an attribute of one article.
     * Example: read(2, "title") will return the title of the article with uid = 2.
     */

    function read($uid, $column)
    {
        pg_prepare($this->handle, "read_column", "select * from articles where uid = $1;");
        $result = pg_execute($this->handle, "read_column", [$uid]);

        if (pg_num_rows($result) != 1)
            return -1;

        else return pg_fetch_result($result, $column);
    }
}
?>
