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


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_ticketsmodule_ticket_bank ADD INDEX idx_ticket_bank (rowid);
-- END MODULEBUILDER INDEXES

ALTER TABLE llx_ticketsmodule_ticket_bank ADD CONSTRAINT fk_ticket_bank_fk_societe FOREIGN KEY (fk_societe) REFERENCES llx_societe (rowid);
-- adding a foreign key on societe rowid

--ALTER TABLE llx_ticketsmodule_ticket_bank ADD UNIQUE INDEX uk_ticketsmodule_myobject_fieldxyz(fieldx, fieldy);

--ALTER TABLE llx_ticketsmodule_ticket_bank ADD CONSTRAINT llx_ticketsmodule_ticket_bank_field_id FOREIGN KEY (fk_field) REFERENCES llx_myotherobject(rowid);
