<?php
header("Content-Type: application/javascript");
echo w3l\scriptify::decode($_GET["js"] ?? "");
