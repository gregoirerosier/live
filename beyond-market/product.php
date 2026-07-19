<?php
$pageTitle='Product — Beyond Market';
require __DIR__.'/includes/header.php';
$id=(string)($_GET['id']??'');
$products=mread('products.json');
$p=null;
foreach($products as $item){if(($item['id']??'')===$id){$p=$item;break;}}
if(!$p){http_response_code(404);echo '<main class="wrap section"><h1>Item not found</h1></main>';require __DIR__.'/includes/footer.php';exit;}
$paymentMethods=$p['payment_methods']??['bits','stripe'];
$bitsOnly=$paymentMethods===['bits'];
$isDownload=($p['delivery']??'')==='download'||($p['surface']??'')==='Digital';
if($_SERVER['REQUEST_METHOD']==='POST'&&csrf_ok()){
  $requested=(string)($_POST['payment']??'bits');
  $payment=in_array($requested,$paymentMethods,true)?$requested:'bits';
  $orders=mread('orders.json');
  $orders[]=['id'=>'BM-'.date('ymd').'-'.random_int(1000,9999),'product_id'=>$id,'buyer'=>market_user(),'payment'=>$payment,'status'=>'pending','created_at'=>date(DATE_ATOM)];
  mwrite('orders.json',$orders);
  market_flash('success',$isDownload?'Unlock request created. The paid file remains gated until checkout is confirmed.':'Order request created. Continue to the selected checkout method.');
  header('Location: product.php?id='.urlencode($id));exit;
}
$success=market_flash('success');
?>
<main class="wrap section">
  <?php if($success):?><div class="notice"><?=me($success)?></div><?php endif;?>
  <div class="hero" style="padding-top:25px">
    <div class="panel art product-hero-art" style="height:480px;--accent:<?=me($p['accent'])?>;font-size:130px"><?php if(!empty($p['image'])):?><img src="<?=me($p['image'])?>" alt="Free preview of <?=me($p['title'])?>"><?php else:?><?=me($p['emoji'])?><?php endif;?></div>
    <div><span class="eyebrow"><?=me($p['category'])?> · <?=me($p['app'])?></span><h1 style="font-size:54px"><?=me($p['title'])?></h1><p><?=me($p['description'])?></p><?php if($isDownload):?><p class="bits-rate">Free gallery preview · Paid file stays locked</p><?php endif;?><p><strong><?=number_format((int)$p['price_bits'])?> bit$</strong><?php if(!$bitsOnly):?> or <strong>$<?=me($p['price_cad'])?> CAD</strong><?php endif;?></p><?php if($bitsOnly):?><p class="bits-rate">Wallet purchase · bit$ only</p><?php endif;?><p class="muted">Created by <a href="creator.php?id=<?=urlencode($p['creator_slug'])?>"><?=me($p['creator'])?></a></p>
      <form method="post" class="panel form"><input type="hidden" name="csrf" value="<?=me(csrf_token())?>"><?php if($bitsOnly):?><input type="hidden" name="payment" value="bits"><p><strong><?=number_format((int)$p['price_bits'])?> bit$ from Wallet</strong></p><?php else:?><label class="label">Payment method<select class="input" name="payment"><option value="bits">Beyond bit$</option><option value="stripe">Card / Stripe</option></select></label><?php endif;?><button class="btn primary"><?=$isDownload?'Unlock download':'Buy now'?></button></form>
    </div>
  </div>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>
