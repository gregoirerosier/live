<?php
require_once __DIR__.'/../includes/app-layout.php';
$wallet=bos_page_start('Beyond Investing','Beyond Investing','Live Bitcoin prices and a transparent bit$ comparison.');
$balance=(float)($wallet['balance']??0);
?>
<main class="bos-main investing-page">
  <section class="bos-hero"><span class="bos-kicker">Beyond Investing · Beta Build 2.1.1</span><h1>Bitcoin now.<br>bit$ in context.</h1><p>Track Bitcoin spot prices in Canadian and U.S. dollars, then compare them with bit$’s fixed ecosystem spend value.</p></section>
  <section class="bos-section market-widget" aria-labelledby="marketWidgetTitle">
    <div class="market-widget-head"><div><span class="bos-kicker">Live market widget</span><h2 id="marketWidgetTitle">BTC · CAD · USD</h2></div><button class="bos-btn secondary" type="button" data-refresh-price>Refresh</button></div>
    <div class="market-grid">
      <article class="market-card bitcoin"><span>Bitcoin / CAD</span><b data-btc-cad>Loading…</b><small>Coinbase spot price</small></article>
      <article class="market-card bitcoin"><span>Bitcoin / USD</span><b data-btc-usd>Loading…</b><small>Coinbase spot price</small></article>
      <article class="market-card bits"><span>bit$ spend value</span><b>100 bit$ = CA$1</b><small>Fixed inside the Beyond ecosystem</small></article>
      <article class="market-card status"><span>Market status</span><b>bit$ is not exchange-traded</b><small>Reward/store value—not a cryptocurrency or investment</small></article>
    </div>
    <p class="market-status" data-price-status role="status">Connecting to live prices…</p>
  </section>
  <section class="bos-section compare-widget"><div><span class="bos-kicker">Comparison calculator</span><h2>Compare your bit$</h2><p>Enter a balance to see its fixed CAD spend value, an approximate USD equivalent, and its informational fraction of one Bitcoin.</p></div><label>bit$ balance<input type="number" min="0" step="1" value="<?=e((string)$balance)?>" data-bits-input></label><div class="compare-results"><div><span>CAD spend value</span><b data-bits-cad>CA$0.00</b></div><div><span>Approx. USD equivalent</span><b data-bits-usd>US$0.00</b></div><div><span>Informational BTC fraction</span><b data-bits-btc>0 BTC</b></div></div></section>
  <section class="bos-section"><div class="bos-notice"><strong>Important distinction</strong><br>Bitcoin’s price moves on external markets. bit$ has no public market price: it is a closed-loop ecosystem reward with a fixed purchase value of CA$0.01 per bit$. The BTC comparison is informational only and does not create convertibility, redemption or an investment product.</div><div class="bos-actions" style="margin-top:14px"><a class="bos-btn" href="<?=e(beyond_url('beyond-market/'))?>">Spend bit$</a><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-finance/'))?>">Back to Wallet</a></div></section>
</main>
<style>
.market-widget,.compare-widget{border:1px solid #ffffff18;border-radius:26px;background:radial-gradient(circle at 90% 0,#f0a62a22,transparent 30%),linear-gradient(145deg,#090c18,#11152a)}.market-widget-head{display:flex;align-items:center;justify-content:space-between;gap:18px}.market-widget h2,.compare-widget h2{margin:6px 0 0;font-size:clamp(30px,5vw,52px)}.market-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:22px}.market-card{display:grid;gap:8px;padding:22px;border:1px solid #ffffff17;border-radius:18px;background:#ffffff08}.market-card span,.compare-results span{color:#9ea6bb;font-size:12px}.market-card b{font-size:clamp(22px,4vw,36px)}.market-card small{color:#727c94}.market-card.bits b{color:#ffe17a}.market-card.status b{font-size:20px;color:#9dd7ff}.market-status{margin:14px 0 0;color:#9099ad;font-size:12px}.compare-widget{display:grid;grid-template-columns:1fr minmax(190px,260px);gap:22px;align-items:end}.compare-widget label{display:grid;gap:7px;color:#aeb5c8;font-size:12px}.compare-widget input{width:100%;padding:14px;border:1px solid #ffffff24;border-radius:12px;background:#070914;color:#fff;font:800 20px system-ui}.compare-results{grid-column:1/-1;display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.compare-results>div{display:grid;gap:7px;padding:17px;border:1px solid #ffffff14;border-radius:15px;background:#ffffff07}.compare-results b{font-size:18px}@media(max-width:700px){.market-grid,.compare-results{grid-template-columns:1fr}.compare-widget{grid-template-columns:1fr}}
</style>
<script>
(function(){
 const cadNode=document.querySelector('[data-btc-cad]'),usdNode=document.querySelector('[data-btc-usd]'),status=document.querySelector('[data-price-status]'),input=document.querySelector('[data-bits-input]');
 let btcCad=0,btcUsd=0;
 const money=(value,currency)=>new Intl.NumberFormat('en-CA',{style:'currency',currency,maximumFractionDigits:2}).format(value);
 function compare(){const bits=Math.max(0,Number(input?.value||0)),cad=bits/100,usd=btcCad>0?cad*(btcUsd/btcCad):0,btc=btcCad>0?cad/btcCad:0;document.querySelector('[data-bits-cad]').textContent=money(cad,'CAD');document.querySelector('[data-bits-usd]').textContent=money(usd,'USD');document.querySelector('[data-bits-btc]').textContent=btc?btc.toPrecision(6)+' BTC':'0 BTC';}
 async function quote(pair){const response=await fetch('https://api.coinbase.com/v2/prices/'+pair+'/spot',{cache:'no-store',headers:{Accept:'application/json'}});if(!response.ok)throw new Error('Price service unavailable');const payload=await response.json();return Number(payload?.data?.amount||0);}
 async function refresh(){status.textContent='Refreshing Bitcoin spot prices…';try{[btcCad,btcUsd]=await Promise.all([quote('BTC-CAD'),quote('BTC-USD')]);if(!btcCad||!btcUsd)throw new Error('Invalid price response');cadNode.textContent=money(btcCad,'CAD');usdNode.textContent=money(btcUsd,'USD');status.textContent='Live spot prices updated '+new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})+'.';compare();}catch(error){cadNode.textContent='Unavailable';usdNode.textContent='Unavailable';status.textContent='Live prices could not be loaded. Try refresh again.';}}
 input?.addEventListener('input',compare);document.querySelector('[data-refresh-price]')?.addEventListener('click',refresh);compare();refresh();setInterval(refresh,60000);
})();
</script>
<?php bos_page_end(); ?>
