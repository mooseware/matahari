Matahari Bundle for the Laravel Framework
=======================================

This bundle enables nicer debugging by spying on vars while a script executes. You could also set time- or memory-marker to see, which time it took to get from start to the specified time-marker, or how much memory is being consumed on a certain point. All markers can be printed out at the end of the espionage.

Here's how to use Matahari:

    Router::register(array('GET /', 'GET /home'), function()
    {
        $i = 0;
        Matahari::init();
        Matahari::mark('before while');
        do
        {
            $i++;
            if ($i % 1000 == 0)
            {
                Matahari::look('before while');
            }
        } while ($i < 10000);

        Matahari::mark('sleep for 2 seconds'); 
        sleep(2);

        $store = array(
            'swords' => 10,
            'coin' => array(
            'copper' => 20,
            'silver' => 50,
            'gold'   => 1000,
        ));
        Matahari::spy($store, "The Store");

        $object = new \stdClass;
        Matahari::spy($object, "The Quatermaster");

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
![Matahari Screenshot](https://github.com/mooseware/matahari/raw/develop/screenshots/screenshot.png)


More 2 come
=======
such as: instantiation with config array for more output ($_REQUEST, $_SESSION etc.)