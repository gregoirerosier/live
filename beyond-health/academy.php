<?php
declare(strict_types=1);
$academyConfig=[
 'slug'=>'beyond-health','title'=>'Beyond Health Academy','icon'=>'❤️','accent'=>'#e3263f','base'=>'/beyond-health/academy.php','css'=>'/beyond-health/academy.css','headline'=>'Understand health. Practice healthy habits.','description'=>'Wellness education inside one unified academy, organized by learning level. Cannabis education remains inside Daily Breath’s adult-only Beyond Green module.','disclaimer'=>'Educational only. Beyond Health does not diagnose conditions or replace a qualified clinician. Seek urgent medical help for emergencies.',
 'tracks'=>[
  'early-learning'=>['My Body & Feelings','Movement & Play','Food & Water','Sleep & Calm','Safety & Helpers'],
  'foundations'=>['Healthy Habits','Fitness & Movement','Nutrition Basics','Sleep & Emotional Wellness','First Aid & Body Safety'],
  'intermediate'=>['Body Changes & Self-Care','Fitness Science','Food, Hydration & Energy','Mental Health Awareness','First Aid & Digital Wellness'],
  'advanced'=>['Personal Wellness Foundations','Training & Recovery','Nutrition Literacy','Stress, Sleep & Mental Health','First Aid, CPR Awareness & Health Decisions'],
  'adult-learning'=>['Daily Health Foundations','Fitness, Mobility & Recovery','Nutrition, Weight & Metabolic Health','Sleep, Stress & Mental Wellness','Preventive Care, First Aid & Health Records']
 ]
];
require dirname(__DIR__).'/includes/learning-academy.php';
