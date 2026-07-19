<?php
require __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
$title='SQL Console';
$query=''; $error=''; $result=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
    $query=trim($_POST['query'] ?? '');
    if($query==='') $error='Enter a SQL command.';
    else {
        try{
            $stmt=$pdo->prepare($query); $stmt->execute();
            if(preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC)\s/i',$query)) $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            else $result=[['message'=>'Query executed successfully','affected_rows'=>$stmt->rowCount()]];
            log_activity($pdo,$_SESSION['user_id'],'sql_console_query');
        }catch(Throwable $e){$error=$e->getMessage();}
    }
}
require __DIR__ . '/../includes/admin-header.php';
require __DIR__ . '/../includes/admin-sidebar.php';
?>
<section class="content"><h1>Protected SQL Console</h1><p class="muted">Admin-only SQL runner. Be careful with destructive commands.</p><div class="card"><?php if($error): ?><div class="badge danger"><?= e($error) ?></div><?php endif; ?><form method="POST"><textarea name="query" rows="8" placeholder="SELECT id, email, role FROM users;"><?= e($query) ?></textarea><p><button>Run SQL</button></p></form></div><?php if(is_array($result)): ?><div class="card"><h2>Results</h2><table><?php if(!empty($result)): ?><tr><?php foreach(array_keys($result[0]) as $col): ?><th><?= e($col) ?></th><?php endforeach; ?></tr><?php foreach($result as $row): ?><tr><?php foreach($row as $v): ?><td><?= e($v) ?></td><?php endforeach; ?></tr><?php endforeach; ?><?php else: ?><tr><td>No results.</td></tr><?php endif; ?></table></div><?php endif; ?></section><?php require __DIR__ . '/../includes/admin-footer.php'; ?>
