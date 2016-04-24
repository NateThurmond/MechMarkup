<?php

if (! isset($_COOKIE["uuid"]))
{
  setcookie("uuid", uniqid(), time()+172800);   //  2 day
}

?>
