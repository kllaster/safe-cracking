<?php
require_once 'config.php';
require_once 'app/Database.php';
require_once 'app/DB_Attempt.php';
require_once 'app/Robber.php';
require_once 'app/utils.php';

global $g_DB;
global $g_max_attempt;

$error = "";
$Bank = $_SESSION['Bank'];
$SafeBox = null;
if (empty($Bank))
	$error = "Bank not specified in cfg";
if (isset($_GET['safe_box']))
    $id_safe = $_GET['safe_box'];
else
	$id_safe = 1;
if (empty($error) && ($SafeBox = $Bank->get($id_safe)) == false)
	$error = "Invalid safe number";
if (empty($error))
{
    $DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
    $DB_Attempt->create_table();
    $log_attempt = $DB_Attempt->select($SafeBox->id);
}

if (empty($_SESSION['robbers']))
    $_SESSION['robbers'][1] = new Robber($g_max_attempt);
else
{
    $time = time();
    foreach ($_SESSION['robbers'] as $key => $item)
    {
		$item = Robber::re_create($item);
		if ($item->lock != 0 && $item->lock < $time)
        {
			$item->lock = 0;
			$item->attempt = 0;
		}
		$_SESSION['robbers'][$key] = $item;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/all.min.css">
    <script src="js/all.min.js"></script>
    <title>Safe-cracking</title>
</head>
<body data-result="<?= $SafeBox->opened ?? 0 ?>">
    <div class="container">
        <h1>Kllaster - Safe-cracking</h1>
		<?php if (!empty($error)): ?>
            <div class="message">
				<?=$error?>
            </div>
		<?php endif; ?>
        <?php if (!empty($SafeBox->opened)): ?>
            <div class="message">The PIN came up: <?=$SafeBox->pin?></div>
		<?php endif; ?>
		<?php if (empty($error)): ?>
            <form action="/" method="get">
                <select name="safe_box" id="safe_box_select">
					<?php foreach ($Bank->safeBoxes as $item): ?>
                        <option value="<?=$item->id?>" <?=$item->id == $SafeBox->id ? 'selected' : ""?>>Safe №<?=$item->id?></option>
					<?php endforeach; ?>
                </select
                ><button class="btn-select-safe" type="submit">Select</button>
            </form>
		<?php endif; ?>
		<?php if (empty($error)): ?>
           <div class="objects_try">
			   <?php $time = time();
                foreach ($_SESSION['robbers'] as $key => $value): ?>
                   <div class="objects_try__item" id="objects_try__item<?=$key?>">
                       <div class="objects_try__item_row">
                           <div class="objects_try__item_name">Robber #<?=$key?></div>
						   <?php if ($value->lock != 0): ?>
                               <div class="objects_try__item_lock" id="lock_<?=$key?>">The safe is blocked: <span><?=$value->lock - $time?> s.</span></div>
						   <?php else: ?>
                               <div class="objects_try__item_count">Attempts: <?=$value->attempt?> / <?=$g_max_attempt?></div>
						   <?php endif; ?>
                       </div>
                       <input class="objects_try__item_input" id="objects_try__item<?=$key?>_input" type="number" max="9999" required
                       ><button class="btn-try">Check</button
                       ><button class="btn-try btn-try-auto">Auto</button>
                   </div>
			   <?php endforeach; ?>
           </div>
        <button class="btn-add-obj">Calling a new robber</button>
		<?php endif; ?>
        <div class="log_attempt">
			<?php if (empty($error) && !empty($log_attempt)):
				for ($i = 0; $i < count($log_attempt); $i++): ?>
                    <div class="log_attempt__item">
                        <div>Robber #<?=$log_attempt[$i]['robber']?></div>
                        <div>PIN: <?=$log_attempt[$i]['pin']?></div>
                        <div><?=$log_attempt[$i]['result'] ? 'Success, the PIN came up!' : "PIN didn't fit"?></div>
                    </div>
				<?php endfor;
			endif; ?>
        </div>
    </div>
</body>
</html>