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
 *   	\file       ticket_bank_withdrawal.php
 *		\ingroup    ticketsmodule
 *		\brief      Page to edit (remove) ticket_bank
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

session_start();

$getCustomersWithTicketsSQL=$db->query('SELECT llx_societe.rowid, nom, half_day_ticket_stored,full_day_ticket_stored FROM llx_societe INNER JOIN llx_ticketsmodule_ticket_bank ON llx_societe.rowid = llx_ticketsmodule_ticket_bank.fk_societe WHERE half_day_ticket_stored > 0 OR full_day_ticket_stored > 0;');

$CompaniesIDStorage = [];
$CompaniesNameStorage = [];
$CompaniesHDTicketStorage = [];
$CompaniesFDTicketStorage = [];

if ($getCustomersWithTicketsSQL) {
  $num = $db->num_rows($getCustomersWithTicketsSQL);
  $i = 0;
  if ($num) {
    while ($i < $num) {
      $obj = $db->fetch_object($getCustomersWithTicketsSQL);
      if ($obj) {
        array_push($CompaniesIDStorage, $obj->rowid);
        array_push($CompaniesNameStorage, $obj->nom);
        array_push($CompaniesHDTicketStorage, $obj->half_day_ticket_stored);
        array_push($CompaniesFDTicketStorage, $obj->full_day_ticket_stored);
      }
      $i++;
    }
  }
}


?>

<h2>Retrait de tickets </h2>

<form method="POST">
  <select name="company_id">
   <?php
    for($i = 0; $i < (count($CompaniesIDStorage)); $i++) {
      echo "<option value=". ($CompaniesIDStorage[$i]) .">". ($CompaniesNameStorage[$i]) ."</option>";
    }
   ?>
  </select>
  <select name="ticket_type">
    <option value="1">Demie Journee</option>
    <option value="2">Journee</option>
  </select>
  <input type="submit" value="Valider" class="button">
</form>


<?php
if(isset($_POST['company_id']) && $_POST['company_id'] != '' && isset($_POST['ticket_type']) && $_POST['ticket_type'] != '') {
  $TicketNumber = 0;
  $WithdrawingCompany  = $CompaniesNameStorage[array_search($_POST['company_id'],$CompaniesIDStorage)];
  $TicketTypeTable = ($_POST['ticket_type'] == '1'? 'half_day_ticket_stored' : 'full_day_ticket_stored' );

  switch ($_POST['ticket_type']) {
      // just add more cases if you have more than 2 type of tickets
    case 'value':
      # code...
      break;

    default:
      $TicketNumber += ($_POST['ticket_type'] == '1' ? $CompaniesHDTicketStorage[array_search($_POST['company_id'],$CompaniesIDStorage)] : $CompaniesFDTicketStorage[array_search($_POST['company_id'],$CompaniesIDStorage)]);
      break;
  }
  $_SESSION['CompanyId'] = $_POST['company_id'];
  $_SESSION['TicketTypeTable'] = $TicketTypeTable;
  $_SESSION['TicketNumber'] = $TicketNumber;

  echo "<p>Sélectionnez le nombre de tickets que vous voulez retirer à ". $WithdrawingCompany ." </p>";
  ?>
  <form method="POST">
    <?php
      echo "<input name=withdrawn_tickets type=number value= 1 min = 1 max=". $TicketNumber .">";
    ?>
    <input type="submit" value="Retrait de tickets" class="button">
  </form>
  <?php
} else {
}

if (isset($_POST['withdrawn_tickets']) && $_POST['withdrawn_tickets'] != '') {
  if( isset($_SESSION['TicketNumber']) && $_POST['withdrawn_tickets'] >= 1 && $_POST['withdrawn_tickets'] <= $_SESSION['TicketNumber']) {
    $RemainingTickets = $_SESSION['TicketNumber'] - $_POST['withdrawn_tickets'];
    $TicketsWithdrawalSQL=$db->query('UPDATE llx_ticketsmodule_ticket_bank SET '. $_SESSION['TicketTypeTable'] .' = '. $RemainingTickets .', last_visit = NOW() WHERE fk_societe = '. $_SESSION['CompanyId'] .';');
    $db->commit();
    echo "<p>Le retrait de ". $_POST['withdrawn_tickets'] ." tickets a bien été effectué !</p>";

  } else {
    echo "<p>Oups, on dirait que quelque chose s'est mal passé ! </p> ";
  }
  unset($_SESSION['CompanyId']);
  unset($_SESSION['TicketTypeTable']);
  unset($_SESSION['TicketNumber']);
} else {
}
// End of page
llxFooter();
$db->close();
