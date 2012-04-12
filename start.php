<?php

Autoloader::map(array(
	'Matahari' => __DIR__.DS.'matahari.php'
));

View::composer('matahari::board', function($view)
{
	$view->output = Matahari::spit()->to_board();
});

Filter::register('matahari', function()
{
	echo View::make('matahari::board');
});