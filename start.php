<?php

Autoloader::map(array(
	'Matahari' => __DIR__.DS.'matahari.php'
));

View::composer('matahari::board', function($view)
{
	$view->output = Matahari::spit()->to_board();
});

Filter::register('matahari_board', function()
{
	echo View::make('matahari::board');
});

Filter::register('matahari_ff', function()
{
	echo Matahari::spit()->to_ff();
});

Filter::register('matahari_chrome', function()
{
	echo Matahari::spit()->to_chrome();
});