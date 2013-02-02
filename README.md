PHPRouter
========

Routing made easy
----------------------------------------

**PHPRouter** is a simple application router, like you can find in [RAILS](http://rubyonrails.org/).

All you have to do is to defines routes, according to your URLs, and specify controller / method associated and implements it with your own MVC.


**PHPRouter** is based on [Dan Sosedoff](http://blog.sosedoff.com/2009/09/20/rails-like-php-url-router/) script.

Installation
----------------------------------------

* Simply download the lastest requestRouter.php package ([zip](http://github.com/mathieulesniak/PHPRouter/zipball/master) or [tarball](http://github.com/mathieulesniak/PHPRouter/tarball/master)) and put requestRouter.php somewhere accessible in your application.
* Create an index.php and use .htaccess to tell Apache to route each URLs through index.php (see .htaccess-sample file)
* Define your routes
* Call requestRouter::doRouting() and let the magic happens ;)


How to use
----------------------------------------

To define new route, use the following syntax : 

	$router->addRoute('my-path/:param1/:param2',
					array('controller' => 'myController', 'method' => 'theMethod'),
					array(
						'param1' => RequestRoute::PARAMETER_TYPE_ALPHA,
						'param2' => RequestRoute::PARAMETER_TYPE_NUMBER
						)
					);

where $router is an instance of requestRouter class.

The example above define the route /my-path with two additionnal parameters param1 and param2.
To define a parameter in a route, you only need to prefix it with ":".

The second part of addRoute() function is to define the controller and the method of that controller used to handle that route. Here, myController::theMethod() will be called.

The third part is used to specify which type of parameter you accept in your route.
It's an associative array, with parameter name as index, and a regular expression as value.

There's two predefined parameters type already available : 
* RequestRoute::PARAMETER_TYPE_ALPHA : [\d\w\s_-]+
* RequestRoute::PARAMETER_TYPE_NUMBER : \d+

You need to use the same name for the parameters in your route and in your controller's method (but not necessary in the same order)
For example, the following controller can be used with the example above :

	class myController
	{
		function __construct() {}

		function theMethod($param2, $param1, $param3)
		{
			echo "Calling zeMethod with $param2 / $param1, $param3";
		}
	}


Dependencies
----------------------------------------
* [PHP 5.2+](http://www.php.net)

License
----------------------------------------
[GNU General Public License](http://opensource.org/licenses/gpl-3.0.html)

Author
----------------------------------------
Mathieu LESNIAK ([mathieu@lesniak.fr](mailto:mathieu@lesniak.fr))