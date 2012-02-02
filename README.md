Matahari Bundle for the Laravel Framework
=======================================

This bundle enables nicer debugging by spying on vars while a script executes. You could also set time- or memory-marker to see, which time it took to get from start to the specified time-marker, or how much memory is being consumed on a certain point. All markers can be printed out at the end of the espionage.

Here's how to use Matahari:

    Router::register(array('GET /', 'GET /home'), function()
    {
        Matahari::init();
        $array = array('test' => 1, 'test2' => 3);
        $array2 = array('foo' => 'bar', 'float' => 1.209);
        Matahari::mark('before while');
        Matahari::memory('before while');
        do {
            $i++;
        } while ($i < 10000000);
        Matahari::mark('after while');
        Matahari::memory('after while');
        Matahari::spy($i, 'contents of $i');
        Matahari::spy($array, 'contents of $array1');
        Matahari::spy($array2, 'contents of $array2');
        Matahari::memory('end');

        return View::make('view.index')->with('output', Matahari::spit()->to_board());
    });

    
Notice
=======
**This bundle is still under development!**


Requirements
=============
- PHP 5.3+
- Laravel PHP 3.0+


Output (example)
=================
![Matahari Screenshot](https://github.com/mooseware/matahari/raw/master/screenshots/v04.png)


More 2 come
=======
such as: instantiation with config array for more output ($_REQUEST, $_SESSION etc.)