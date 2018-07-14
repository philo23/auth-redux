<?php
$db_file = realpath(__DIR__) . '/sqlite.db';

$db = new PDO('sqlite:' . $db_file);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

return $db;
