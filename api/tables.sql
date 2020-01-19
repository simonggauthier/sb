create table users (id varchar (128), password_hash varchar (128), primary key (id));
create table login_tokens (id varchar (128), owner_id varchar (128), device varchar (128), use_date datetime);

create table books (id int auto_increment, name varchar (256), owner_id varchar (128), primary key (id));
create table categories (id int auto_increment, book_id int, name varchar (256), color varchar (6), primary key (id));
create table transactions (id int auto_increment, book_id int, direction varchar (10), title varchar (256), category_id int, amount int, creation_date datetime, primary key (id));
