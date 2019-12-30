create table users (id varchar (128), password_hash varchar (128), primary key (id));
create table objects (id varchar (128), value text, owner_id varchar (128), primary key (id, owner_id));

create table login_tokens (id varchar (128), owner_id varchar (128), device varchar (128), use_date datetime);
