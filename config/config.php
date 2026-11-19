<?php
declare(strict_types=1);

// ---- Database (change to match your XAMPP / Laragon / MySQL setup) ----
const DB_HOST = 'localhost';
const DB_NAME = 'portfolio_db';
const DB_USER = 'root';
const DB_PASS = '';          // XAMPP default is empty. Use a strong password in production!

// ---- App ----
const APP_DEBUG       = false;   // true only on your own computer while developing
const SESSION_IDLE    = 1800;    // admin auto-logout after 30 min of inactivity
const MAX_LOGIN_FAILS = 5;       // failed logins allowed ...
const LOGIN_WINDOW    = 15;      // ... per this many minutes (per IP)
const MAX_MSG_PER_HR  = 5;       // contact messages allowed per IP per hour
const MAX_UPLOAD      = 2097152; // 2 MB

date_default_timezone_set('Asia/Manila');
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
