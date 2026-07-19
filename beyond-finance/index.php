<?php
require_once __DIR__.'/../includes/app-layout.php';
$wallet=bos_page_start('Beyond Wallet','Beyond Wallet','Spend bit$, track purchases and manage verified earnings.');
$bits=number_format((float)($wallet['balance']??0));
?>
<main class="bos-main">
  <section class="bos-hero"><span class="bos-kicker">Beyond Wallet · Beta Build 2.1.1</span><h1>Your wallet.</h1><p>Spend rewards across the ecosystem, review purchases and cash out verified creator earnings after payout setup.</p></section>
  <section class="bos-section"><div class="bos-stat-grid"><div class="bos-stat"><b><?=$bits?></b><span>Spendable bit$</span></div><div class="bos-stat"><b>CA$<?=number_format((float)($wallet['balance']??0)/100,2)?></b><span>Ecosystem spend value</span></div><div class="bos-stat"><b>CA$0.00</b><span>Verified CAD earnings</span></div><div class="bos-stat"><b>USD 0.00</b><span>Verified USD earnings</span></div></div></section>
  <section class="bos-section"><div class="bos-notice"><strong>Spend your bit$. Cash out your earnings.</strong><br>Every 100 bit$ carries CA$1 of ecosystem spend value. Reward bit$ are for purchases; verified creator earnings remain real CAD or USD for withdrawal after payout setup.</div><div class="bos-actions" style="margin-top:14px"><a class="bos-btn" href="<?=e(beyond_url('beyond-market/'))?>">Spend bit$</a><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-id/dashboard/wallet.php'))?>">Wallet activity</a></div></section>
  <section class="bos-section"><a href="<?=e(beyond_url('beyond-market/'))?>" style="display:block;overflow:hidden;border-radius:24px"><img src="<?=e(beyond_url('beyond-market/assets/img/ecosystem-wallet-banner.png'))?>" alt="The Beyond ecosystem connected through Wallet" style="display:block;width:100%;height:auto"></a><div class="bos-actions" style="margin-top:14px"><a class="bos-btn" href="<?=e(beyond_url('beyond-market/?category=Tattoo+Flash'))?>">Preview stencils →</a></div></section>
  <section class="bos-section"><h2>Wallet tools</h2><div class="bos-grid"><?php echo bos_app_card('Transactions','See bit$ and cash activity.','beyond-id/dashboard/wallet.php','↔'); echo bos_app_card('Beyond Investing','Live BTC prices in CAD and USD with a transparent bit$ comparison.','beyond-investing/','₿','Live'); echo bos_app_card('Budget','Plan monthly income and expenses.','beyond-finance/budget.php','▥','Beta'); echo bos_app_card('Savings Goals','Track targets and milestones.','beyond-finance/goals.php','🎯','Beta'); ?></div></section>
</main>
<?php bos_page_end(); ?>
