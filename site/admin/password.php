<?php require 'inc/auth.php';require 'inc/layout.php';$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$s=db()->prepare("SELECT password FROM admins WHERE id=?");$s->execute([$_SESSION['admin']]);
if(!password_verify($_POST['old']??'',$s->fetchColumn()))$m='<div class="alert">Password lama salah.</div>';elseif(strlen($_POST['new']??'')<8)$m='<div class="alert">Password baru minimal 8 karakter.</div>';
else{db()->prepare("UPDATE admins SET password=? WHERE id=?")->execute([password_hash($_POST['new'],PASSWORD_DEFAULT),$_SESSION['admin']]);$m='<div class="ok">Password diperbarui.</div>';}}
adm_head('Ganti Password');echo $m;?><form method="post" class="card frm" style="margin:0"><?=csrf_field()?><label>Password Lama<input type="password" name="old" required></label><label>Password Baru<input type="password" name="new" required></label><button class="btn">SIMPAN</button></form><?php adm_foot();
