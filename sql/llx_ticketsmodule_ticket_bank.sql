-- Copyright (C) 2018 PopPlace
-- Copyright (C) 2018 ModifiedChocolateUnicorn
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see http://www.gnu.org/licenses/.


CREATE TABLE llx_ticketsmodule_ticket_bank(
	-- BEGIN MODULEBUILDER FIELDS
	rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
	entity INTEGER DEFAULT 1 NOT NULL,
	label VARCHAR(255),
	qty INTEGER,
	status INTEGER,
	date_creation DATETIME NOT NULL,
	tms TIMESTAMP NOT NULL,
	import_key VARCHAR(14),
	-- END MODULEBUILDER FIELDS
	-- BEGIN MANUALLY ADDED FIELDS
	fk_societe integer,
		-- societe identifier
	full_day_ticket_stored INTEGER DEFAULT 0 NOT NULL CHECK (full_day_ticket_stored >= 0),
		-- full day ticket nbr/on 'account'
	half_day_ticket_stored INTEGER DEFAULT 0 NOT NULL CHECK (half_day_ticket_stored >= 0),
		-- half-day ticket nbr/on 'account'
	last_visit DATE
		-- last visit / time a ticket was used
	-- END MANUALLY ADDED FIELDS
) ENGINE=innodb;