<?php require 'inc/auth.php';require 'inc/layout.php';require 'inc/crud.php';
crud('units','Kelola Unit',['name'=>['Nama Unit','text'],'status'=>['Status','select',['available'=>'Available','booked'=>'Booked','maintenance'=>'Maintenance','inactive'=>'Inactive']],'notes'=>['Catatan','textarea']],['name','status','notes'],'id');
