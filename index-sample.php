<?php
require 'request_router.php';

$router = new RequestRouter();

$router->addRoute('my-path/:param1/:param2',
					array('controller' => 'myController', 'method' => 'theMethod'),
					array(
						'param1' => RequestRoute::PARAMETER_TYPE_ALPHA,
						'param2' => RequestRoute::PARAMETER_TYPE_NUMBER
						)
					);

$router->doRouting();
?>