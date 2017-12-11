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

create table articles(
  uid serial,
  timestamp timestamp not null default current_timestamp,
  author char(60) not null,
  title varchar(200) not null,
  text text,
primary key (uid));

create user nanoblog;
grant select, update, insert, delete on table articles to nanoblog;
grant all privileges on sequence articles_uid_seq to nanoblog;
