<?php
require_once __DIR__.'/../../inc/lib.php';
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax']);session_start();
if(empty($_SESSION['admin'])){header('Location: login.php');exit;}
function csrf(){return $_SESSION['csrf']??($_SESSION['csrf']=bin2hex(random_bytes(16)));}
function csrf_field(){return '<input type="hidden" name="csrf" value="'.csrf().'">';}
function csrf_check(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(400);exit('Sesi tidak valid, muat ulang halaman.');}}
