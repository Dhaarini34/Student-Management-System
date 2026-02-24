<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const DB_HOST = '127.0.0.1';
const DB_NAME = 'mentoring_system';
const DB_USER = 'root';
const DB_PASS = '';
const BASE_URL = '/mentoring-system';
const APP_NAME = 'Student Mentoring Management System';

date_default_timezone_set('Asia/Jakarta');
