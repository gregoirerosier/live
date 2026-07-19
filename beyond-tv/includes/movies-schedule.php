<?php
declare(strict_types=1);
function beyond_movies_schedule_state(?DateTimeImmutable $now=null): array {
    $tz=new DateTimeZone('America/Vancouver'); $now=$now?->setTimezone($tz)??new DateTimeImmutable('now',$tz);
    $movies=[
      1=>['title'=>'Cats','id'=>'QrnXZgFYMbk','genre'=>'Family · Adventure','runtime'=>'1 hr 30 min'],
      2=>['title'=>'Mud (2012)','id'=>'qbhV8m2wrZM','genre'=>'Drama','runtime'=>'2 hr 10 min'],
      3=>['title'=>'Never Back Down','id'=>'y886zL1bwQU','genre'=>'Action · Martial Arts','runtime'=>'Full movie'],
      4=>['title'=>'Big Stan','id'=>'sx8pViXxZQg','genre'=>'Comedy','runtime'=>'Full movie'],
      5=>['title'=>'In the Mix','id'=>'zhue70cwb7Y','genre'=>'Romantic Comedy','runtime'=>'Full movie'],
      6=>['title'=>"Don't Look Away",'id'=>'eDrk1ifu0g8','genre'=>'Horror','runtime'=>'Full movie'],
    ];
    $specials=['2026-07-17'=>['title'=>'Jeepers Creepers','id'=>'ROY1YDlYUNc','genre'=>'Horror · Special Presentation','runtime'=>'Full movie']];
    $day=(int)$now->format('N'); $date=$now->format('Y-m-d');
    if(isset($specials[$date])){$current=$specials[$date];$label="TODAY'S SPECIAL";$next=$movies[6];}
    elseif($day===7){
      $slot=(int)floor(((int)$now->format('G')*60+(int)$now->format('i'))/(24*60/6));
      $slot=max(0,min(5,$slot)); $current=$movies[$slot+1]; $label='SUNDAY MARATHON'; $next=$movies[(($slot+1)%6)+1];
    } else {$current=$movies[$day];$label='FEATURE OF THE DAY';$next=$day===6?$movies[1]:$movies[$day+1];}
    $embed='https://www.youtube-nocookie.com/embed/'.$current['id'].'?autoplay=1&mute=1&playsinline=1&rel=0&modestbranding=1';
    return ['current'=>$current,'next'=>$next,'label'=>$label,'embed_url'=>$embed,'movies'=>$movies,'is_marathon'=>$day===7,'date'=>$date];
}
