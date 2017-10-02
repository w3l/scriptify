<?php
header("Content-Type: application/javascript");
echo (new w3l\scriptify)->decode($_GET["js"] ?? "");
