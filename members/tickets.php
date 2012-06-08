<?php
include('../include/members.php');

limitAccess(array(2));

$_tpl->display("members_tickets.tpl");
?>