****************
Notejam: Asgard
****************

Notejam application implemented using `Asgard <http://asgardphp.net>`_ framework.

Asgard version: 0.2

==========================
Installation and launching
==========================

-----
Clone
-----

Clone the repo:

.. code-block:: bash

    $ git clone git@github.com:asgardphp/notejam.git YOUR_PROJECT_DIR

-------
Install
-------

Install `composer <https://getcomposer.org/>`_

.. code-block:: bash

    $ cd YOUR_PROJECT_DIR
    $ curl -s https://getcomposer.org/installer | php

Install dependencies

.. code-block:: bash

    $ cd YOUR_PROJECT_DIR
    $ php composer.phar install

Create database schema

.. code-block:: bash

    $ cd YOUR_PROJECT_DIR
    $ php console db:create
    $ php console migrate


------
Launch
------

Start built-in asgard web server:

.. code-block:: bash

    $ cd YOUR_PROJECT_DIR/web
    $ php console server

Go to http://localhost:8000/ in your browser.

---------
Run tests
---------

Run tests:

.. code-block:: bash

    $ cd YOUR_PROJECT_DIR
    $ php vendor/bin/phpunit


============
Contribution
============
Do you have php/asgard experience? Help the app to follow php and asgard best practices.

Please send your pull requests in the ``master`` branch.
Always prepend your commits with framework name:

.. code-block:: bash

    Asgard: Implemented sign in functionality

Read `contribution guide <https://github.com/komarserjio/notejam/blob/master/contribute.rst>`_ for details.