<link href="<?php echo URL::to_asset('bundles/matahari/css/matahari.css'); ?>" rel="stylesheet" />

<div id="mata_hari_debug">
	<div id="title">Mata Hari - exotic espionage for PHP</div>
	<div id="mata_hari_values">
	<div class="meta-info">
		Total Execution Time: <span class="time"><?php echo $output['total_time']; ?>s</span>
		<br />
		Total Consumed Memory: <span class="memory"><?php echo $output['total_memory']; ?>Mb</span>
	</div>

	<?php
	$i = 1;
	$odd_even = 'even';

	foreach ($output['items'] as $item)
	{
		if ($item['name'] == '')
		{
			$item['name'] = '#'.$i;
		}

		echo '<div class="spy-marker-time '.$odd_even.'">';

		switch ($item['type'])
		{
			case 'marker':
				echo sprintf(
					'Marked <span class="marker-name">%s</span> [ <span class="time">%ss</span> <span class="memory">%sMb</span> ]', 
					$item['name'], 
					round(($item['time'] - Matahari::$start), 4), 
					round($item['memory'] / pow(1024, 2), 3)
				);
				break;

			case 'look':
				echo sprintf(
					'Look at <span class="marker-name">%s</span> [ <span class="time">%ss</span> <span class="memory">%sMb</span> (<span class="memory">%sMb</span>) ]',
					$item['name'],
					$item['time_diff'],
					round($item['current_memory'] / pow(1024, 2), 3),
					$item['memory_diff']
				);
				break;
			
			case 'spy':
				echo sprintf(
					'Spying on <span class="marker-name">%s</span>%s',
					$item['name'],
					'<pre>'.$item['content'].'</pre>'
				);
				break;

			case 'memory':
				echo sprintf(
					'Memory consumed at marker <span class="marker-name">%s</span></em>: <span class="time">%sMb</span>',
					$item['name'],
					round($item['memory'] / pow(1024, 2), 3)
				);
				break;
		}

		echo '</div>';

		$i++;
		($odd_even == 'even') ? $odd_even = 'odd' : $odd_even = 'even';
	} // end foreach
	?>

	</div>
	<br style="clear: both;" />
</div>