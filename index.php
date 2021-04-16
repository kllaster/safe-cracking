<?php
    require_once 'config.php';
    require_once 'app/Database.php';
    require_once 'app/DB_Attempt.php';
    require_once 'app/utils.php';

    global $g_DB;
    global $g_safe;

    $error = "";
	$id_safe = $_GET['safe_box'];
    $start = $_GET['start'];
    if (empty($id_safe))
    {
		if (!empty($g_safe))
			$id_safe = array_key_first($g_safe);
		else
			$error = "Safe not specified in cfg";
    }
	else if (!array_key_exists($id_safe, $g_safe))
		$error = "Invalid safe number";
	if (empty($error))
    {
		$DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
		$DB_Attempt->create_table();
		$log_attempt = $DB_Attempt->select($id_safe);
    }

    if (empty($_SESSION['objs']))
		$_SESSION['objs'][1]['attempt'] = 0;
    else
    {
        $time = time();
        foreach ($_SESSION['objs'] as $key => $value)
        {
			if (!empty($value['lock']) && $value['lock'] < $time)
            {
				$_SESSION['objs'][$key]['lock'] = 0;
				$_SESSION['objs'][$key]['attempt'] = 0;
			}
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
<body>
    <div class="container">
        <h1>Kllaster - Safe-cracking</h1>
		<? if (!empty($error)): ?>
            <div class="message">
				<?=$error?>
            </div>
		<? endif; ?>
		<? if (!empty($_SESSION['result'][$id_safe])): ?>
            <div class="message">The PIN came up: <?=$_SESSION['result'][$id_safe]?></div>
		<? endif; ?>
		<? if (empty($error)): ?>
            <form action="/" method="get">
                <select name="safe_box" id="safe_box_select">
					<?php foreach ($g_safe as $key => $item): ?>
                        <option value="<?=$key?>" <?=$key == $id_safe ? 'selected' : ""?>>Safe №<?=$key?></option>
					<?php endforeach; ?>
                </select
                ><button class="btn-select-safe" type="submit">Select</button>
            </form>
		<? endif; ?>
		<? if (empty($error)): ?>
           <div class="objects_try">
			   <?php $time = time();
                foreach ($_SESSION['objs'] as $key => $value): ?>
                   <div class="objects_try__item" id="objects_try__item<?=$key?>">
                       <div class="objects_try__item_row">
                           <div class="objects_try__item_name">Object #<?=$key?></div>
						   <? if (!empty($value['lock'])): ?>
                               <div class="objects_try__item_lock" id="lock_<?=$key?>">The safe is blocked: <span><?=$value['lock'] - $time?> s.</span></div>
						   <? else: ?>
                               <div class="objects_try__item_count">Attempts: <?=$value['attempt']?> / 10</div>
						   <? endif; ?>
                       </div>
                       <input class="objects_try__item_input" id="objects_try__item<?=$key?>_input" type="number" max="9999" required
                       ><button class="btn-try">Check</button
                       ><button class="btn-try btn-try-auto">Auto</button>
                   </div>
			   <?php endforeach; ?>
           </div>
        <button class="btn-add-obj">Creating a new object for brute-force</button>
		<? endif; ?>
        <div class="log_attempt">
			<?php if (empty($error) && !empty($log_attempt)):
				for ($i = 0; $i < count($log_attempt); $i++): ?>
                    <div class="log_attempt__item">
                        <div>Object #<?=$log_attempt[$i]['object']?></div>
                        <div>PIN: <?=$log_attempt[$i]['pin']?></div>
                        <div><?=$log_attempt[$i]['result'] ? 'Success, the PIN came up!' : "PIN didn't fit"?></div>
                    </div>
				<?php endfor;
			endif; ?>
        </div>
    </div>
</body>
</html>