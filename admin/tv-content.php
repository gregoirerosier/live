<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/app-layout.php';
require_once __DIR__.'/../beyond-tv/includes/youtube-api.php';
bos_require_admin();
$file=__DIR__.'/../beyond-tv/data/channels.json';
$channels=json_decode((string)@file_get_contents($file),true)?:[];
$message='';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $slug=(string)($_POST['slug']??'');
  $input=(string)($_POST['youtube_id']??'');
  $youtubeId=beyond_youtube_extract_id($input);
  $clear=isset($_POST['clear']);

  if($clear){
    $youtubeId='';
  } elseif($youtubeId==='') {
    $error='Enter a valid YouTube URL or 11-character video ID.';
  }

  $metadata=[];
  if(!$error && $youtubeId!==''){
    $validation=beyond_youtube_video($youtubeId);
    if(!($validation['ok']??false)){
      $error=(string)($validation['error']??'YouTube validation failed.');
    } else {
      $metadata=(array)($validation['video']??[]);
    }
  }

  if(!$error){
    $found=false;
    foreach($channels as &$channel){
      if(($channel['slug']??'')===$slug && ($channel['source_type']??'')==='youtube_embed'){
        $found=true;
        $channel['youtube_id']=$youtubeId;
        $channel['approved_at']=$youtubeId!==''?date('c'):'';
        $channel['approved_by']=$youtubeId!==''?'admin':'';
        if($youtubeId!==''){
          $channel['youtube_title']=(string)($metadata['title']??'');
          $channel['youtube_channel_title']=(string)($metadata['channel_title']??'');
          $channel['youtube_channel_id']=(string)($metadata['channel_id']??'');
          $channel['youtube_thumbnail']=(string)($metadata['thumbnail']??'');
          $channel['youtube_duration']=(string)($metadata['duration']??'');
          $channel['youtube_live_status']=(string)($metadata['live_status']??'none');
          $channel['youtube_verified_at']=date('c');
          $channel['now']=(string)($metadata['title']??$channel['now']??'Official YouTube video');
          $message='Official embeddable video verified and saved.';
        } else {
          foreach(['youtube_title','youtube_channel_title','youtube_channel_id','youtube_thumbnail','youtube_duration','youtube_live_status','youtube_verified_at'] as $field) unset($channel[$field]);
          $message='Embed cleared.';
        }
        break;
      }
    }
    unset($channel);
    if(!$found){
      $error='Series channel was not found.';
    } else {
      $encoded=json_encode($channels,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
      if($encoded===false || file_put_contents($file,$encoded."\n",LOCK_EX)===false){
        $error='Unable to write channels.json. Check file permissions.';
        $message='';
      }
    }
  }
}
bos_page_start('TV Content','Beyond TV Content Manager','Approve official YouTube embeds without downloading or rehosting copyrighted programs.');
?>
<main class="bos-main"><section class="bos-hero"><span class="bos-kicker">Beyond TV 2.2 Beta</span><h1>Cartoon series manager</h1><p>Paste the 11-character YouTube video ID from an official rights-holder upload. Beyond TV uses privacy-enhanced embedding and keeps the media on YouTube.</p><?php if($message):?><p class="bos-notice"><?=htmlspecialchars($message)?></p><?php endif;?><?php if($error):?><p class="bos-notice" style="border-color:#ef4444;color:#fecaca"><?=htmlspecialchars($error)?></p><?php endif;?></section>
<section class="bos-section"><p><a class="bos-btn" href="/admin/tv-episodes.php">Open episode catalogue →</a></p><div class="bos-grid">
<?php foreach($channels as $channel): if(($channel['source_type']??'')!=='youtube_embed')continue; ?>
<form method="post" class="bos-card"><span style="font-size:2rem"><?=htmlspecialchars((string)$channel['icon'])?></span><h2><?=htmlspecialchars((string)$channel['name'])?></h2><p><?=htmlspecialchars((string)($channel['official_channel']??''))?> · <?=htmlspecialchars((string)($channel['rating']??''))?></p><?php if(!empty($channel['youtube_title'])):?><p><strong><?=htmlspecialchars((string)$channel['youtube_title'])?></strong><br><small>Verified from <?=htmlspecialchars((string)($channel['youtube_channel_title']??'YouTube'))?> · <?=htmlspecialchars((string)($channel['youtube_verified_at']??''))?></small></p><?php endif;?><input type="hidden" name="slug" value="<?=htmlspecialchars((string)$channel['slug'])?>"><label>Official YouTube URL or video ID<input name="youtube_id" value="<?=htmlspecialchars((string)($channel['youtube_id']??''))?>" maxlength="180" placeholder="https://www.youtube.com/watch?v=..." style="width:100%;margin:10px 0;padding:12px;border-radius:10px"></label><button class="bos-btn" type="submit">Verify & save</button><?php if(!empty($channel['youtube_id'])):?><button class="bos-btn secondary" type="submit" name="clear" value="1">Clear</button><?php endif;?><a class="bos-btn secondary" target="_blank" rel="noopener" href="<?=htmlspecialchars((string)($channel['official_url']??'#'))?>">Open official channel</a></form>
<?php endforeach; ?></div></section></main><?php bos_page_end(); ?>
