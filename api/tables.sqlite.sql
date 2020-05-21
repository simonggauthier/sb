create table users (id TEXT PRIMARY KEY, password_hash TEXT);
create table login_tokens (id TEXT PRIMARY KEY, owner_id TEXT, device TEXT, last_use_date INTEGER);
create table books (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, owner_id TEXT);
create table categories (id INTEGER PRIMARY KEY AUTOINCREMENT, book_id INTEGER, name TEXT, color TEXT);
create table transactions (id INTEGER PRIMARY KEY AUTOINCREMENT, book_id INTEGER, direction TEXT, title TEXT, category_id INTEGER, amount INTEGER, creation_date INTEGER);
