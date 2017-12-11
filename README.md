# nanoblog

## Description

nanoblog is a very basic blogware, started as a training project for high school. There is no database abstraction layer, so you're required to use PostgreSQL. The software allows the user to create, edit and delete articles, generate permalinks for articles, and display all articles on multiple pages.

Blog settings can be changed in the `config.xml` configuration file. Here the admin can set the blog's name, the database host and the database name. Username and password are both used by nanoblog to authenticate to the PostgreSQL server and for the admin to log in to nanoblog itself. The configuration file must not be viewable by others. You can use the included setup.sql to initialize your database.

## License

nanoblog is brought to you under the conditions of the [GNU Affero General Public License Version 3][1].

The default background image "Dark Wood" was created by Omar Alvarado, licensed under [CC-BY-SA 3.0][2], received from [Subtle Patterns][3].

[1]: http://www.gnu.org/licenses
[2]: http://creativecommons.org/licenses/by-sa/3.0/deed.en_US
[3]: http://subtlepatterns.com/dark-wood
