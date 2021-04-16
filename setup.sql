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