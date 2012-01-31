Matahari Bundle for the Laravel Framework
=======================================

This bundle enables nicer debugging by spying on vars while a script executes. All markers can be spit out together at the end of the espionage.

Here's how to use Matahari:

    Router::register(array('GET /', 'GET /home'), function()
    {
        $matahari = new Matahari;
        $matahari->spy('test-marker', array(1,2,3,4));
        $output = $matahari->spit();
        return View::make('view.index')->with('output', $output);
    });