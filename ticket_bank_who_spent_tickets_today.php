<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018 PopPlace
 * Copyright (C) 2018 ModifiedChocolateUnicorn

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       ticket_bank_who_spent_tickets_today.php
 *		\ingroup    ticketsmodule
 *		\brief      Page to see who spent a ticket today and came to the coworking space
 */

 // Load Dolibarr environment
 $res=0;
 // Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
 if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
 // Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
 $tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
 while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
 if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
 if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
 // Try main.inc.php using relative path
 if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
 if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
 if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
 if (! $res) die("Include of main fails");

dol_include_once('/ticketsmodule/class/ticket_bank.class.php');
DOL_DOCUMENT_ROOT.'societe/class/societe.class.php';

llxHeader("",$langs->trans("TicketsModuleArea"));

$todayCustomersStorage = [];

$getCustomersWhoSpentTicketsSQL=$db->query('SELECT nom
  FROM llx_societe
  INNER JOIN llx_ticketsmodule_ticket_bank ON llx_societe.rowid = llx_ticketsmodule_ticket_bank.fk_societe
  WHERE last_visit = CURDATE()');

if ($getCustomersWhoSpentTicketsSQL) {
  $num = $db->num_rows($getCustomersWhoSpentTicketsSQL);
  $i = 0;
  if ($num) {
    while ($i < $num) {
      $obj = $db->fetch_object($getCustomersWhoSpentTicketsSQL);
      if ($obj) {
        array_push($todayCustomersStorage, $obj->nom);
      }
      $i++;
    }
  }
}
?>
<div>
  <h3>Aujourd'hui nous avons eu le plaisir d'accueillir : </h3>

<?php
for($i = 0; $i < count($todayCustomersStorage); $i++) {
  echo '<p>'. $todayCustomersStorage[$i] .'</p>';
}
?>
</div>
<?php
$db->close();
