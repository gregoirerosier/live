<?php require_once __DIR__.'/../includes/auth.php'; logout_user(); flash('success','Signed out.'); redirect('auth/login.php');
