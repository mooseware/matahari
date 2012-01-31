Matahari Bundle for the Laravel Framework
=======================================

This bundle enables nicer debugging by spying on vars while a script executes. All markers can be spit out together at the end of the espionage. Markers with the same name are being grouped.

Here's how to use Matahari:

    Router::register(array('GET /', 'GET /home'), function()
    {
        Matahari::spy('test-marker', array(1,2,3,4));
        Matahari::spy('test-marker', array(5,6,7,8));
        Matahari::spy('test-marker2', array('key' => 'test'));

        return View::make('intro.index')->with('output', Matahari::spit());
    });

    
Notice
=======
This bundle is still under development!


Output
=======
![Matahari Screenshot](https://github.com/mooseware/matahari/raw/master/screenshots/matahari.png)