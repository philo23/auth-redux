<?php

$db->exec("
  CREATE TABLE users (
    id INTEGER PRIMARY KEY NOT NULL,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    extra_data TEXT NOT NULL
  )
");

$db->exec("
  CREATE TABLE auth_tokens (
    token TEXT PRIMARY KEY NOT NULL,
    username TEXT NOT NULL,
    created_at TEXT NOT NULL,
    expires_at TEXT NOT NULL
  )
");

$stmt = $db->prepare("
  INSERT INTO users (username, password, extra_data) VALUES (?, ?, ?)
");

$stmt->execute([
    "philip",
    password_hash('testing', PASSWORD_DEFAULT),
    bin2hex(random_bytes(10))
]);
$stmt->execute([
    'alice',
    password_hash('test', PASSWORD_DEFAULT),
    bin2hex(random_bytes(10))
]);
$stmt->execute([
    "bob",
    password_hash('test', PASSWORD_DEFAULT),
    bin2hex(random_bytes(10))
]);
