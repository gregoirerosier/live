<?php
function verify_email_template(string $name, string $verifyUrl): string {
    $safeName = htmlspecialchars($name ?: 'there', ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8');
    return "
    <div style='background:#0b1020;padding:28px;font-family:Arial,sans-serif;color:#fff'>
      <div style='max-width:560px;margin:auto;background:#121a2b;border:1px solid #29354d;border-radius:24px;padding:28px'>
        <h1 style='margin:0 0 10px;font-size:28px'>🍽 Beyond Catering</h1>
        <p style='font-size:18px;color:#cbd5e1'>Hi {$safeName}, verify your email to continue onboarding.</p>
        <a href='{$safeUrl}' style='display:inline-block;background:linear-gradient(135deg,#ff7a00,#ffb347);color:#111;text-decoration:none;font-weight:800;padding:16px 22px;border-radius:16px;margin:18px 0'>Verify Email</a>
        <p style='color:#94a3b8;font-size:14px'>This link expires in 24 hours. If you did not create this account, ignore this email.</p>
      </div>
    </div>";
}
