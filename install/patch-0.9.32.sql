ALTER TABLE categorydef CHANGE rating rating smallint not null default '0';
UPDATE categorydef SET rating=rating-13;
UPDATE categorydef SET rating=0 WHERE rating='-13';
ALTER TABLE rating CHANGE value value smallint not null default '0';
UPDATE rating SET value=value-13;