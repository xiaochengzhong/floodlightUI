<?php
$pos = $_GET['pos'];//pos是文件索引位置
if(0 == $pos)
{
	echo json_encode(firstGet(50));
}else
{
	echo json_encode(getAll($pos));
}


function getLogPath()
{
	return '/var/lib/floodlight/floodlight.log';
}

function firstGet($limit = 50)
{
	$logPath = getLogPath();
	if(!$logPath)
	{
		echo "There is no log!";
		exit;
	}
	if(file_exists($logPath))
	{
		$fp = fopen($logPath, 'r');
		fseek($fp, 0, SEEK_END);
		$end_pos = ftell($fp);
		$pos = -2;
		$t = ' ';
		for($i = 0; $i < $limit; $i++)
		{
			while("\n" != $t)
			{
				fseek($fp, $pos, SEEK_END);
				$t = fgetc($fp);
				if(null == $t) break;
				$pos--;
			}
			$t = " ";
			$line = fgets($fp);
			$ar_log [] = $line;
			if(false == $line) break;
		}
		fclose($fp);
		return array('log_list'=>array_reverse($ar_log), 'pos'=>$end_pos);
	}
}

function getAll($pos, $limit = 50)
{
	set_time_limit(0);
	$logPath = getLogPath();
	if(!$logPath)
	{
		echo "There is no log!";
		exit;
	}
	if(file_exists($logPath))
	{
		$fp = fopen($logPath, 'r');
		fseek($fp, $pos, SEEK_SET);
		$i = 0;
		$t = '';
		$ar_log = array();
		while($i < $limit && false !== $t)
		{
			$t = ' ';
			$line = fgets($fp);
			if($line)
				$ar_log [] = $line;
			$i++;
			while("\n" != $t)
			{
				fseek($fp, $pos, SEEK_SET);
				$t = fgetc($fp);
				if(false === $t)
				{
					$ret ['log_end'] = 1;
					break;
				}
				$pos++;
			}
		}	
	}
	$ret ['log_list'] = $ar_log;
	$ret ['pos']	  = $pos--;
	return $ret;
}
?>
