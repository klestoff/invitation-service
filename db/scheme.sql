create table "guest" (
	"id"					serial primary key,
	"name"					varchar(255) not null,
	"display_name"			varchar(255) not null,
	"file_name"				varchar(255) not null,
	"result_id"				smallint not null default 1,
	"quantity_id"			smallint not null default 1,
	"secret_key"			varchar(40) not null,
	"open_key"				varchar(40) not null,
	"optional_text"			varchar(100),
	"optional_result_id"	smallint not null default 1,
	"comment"				text
);

alter sequence guest_id_seq rename to guest_id;

create unique index "guest_secret_key_idx" on "guest"("secret_key");
create unique index "guest_open_key_idx" on "guest"("open_key");
