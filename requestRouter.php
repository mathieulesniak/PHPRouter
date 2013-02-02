<?php
class RequestRouter
{
	private $requestUri;
	private $routes;

	function __construct()
	{
		$this->routes = array();
		$this->parseRequest();
	}

	private function parseRequest()
	{
		$this->requestUri = $_SERVER['REQUEST_URI'];
	}

	public function addRoute($routeName, $routeTarget, $parametersDefinitions = array())
	{
		$this->routes[$routeName] = new RequestRoute($routeName, $this->requestUri, $routeTarget, $parametersDefinitions);
	}

	public function doRouting()
	{
		$hasRoute = false;
		foreach ($this->routes as $routeName=>$route )
		{
			if ( $route->isMatched )
			{
				// We found a valid route

				if ( class_exists($route->controller) )
				{
					// We found a valid controller

					$controller = new $route->controller();
					if ( method_exists($controller, $route->method) )
					{
						// We found a valid method for this controller
						// Find parameters order 
						$reflection = new ReflectionMethod($controller, $route->method);
						$methodParameters = $reflection->getParameters();
						$methodArguments = array();
						foreach ($methodParameters as $index=>$parameter )
						{
							if ( isset($route->parametersValues[$parameter->name]) )
							{
								$methodArguments[$index] = urldecode($route->parametersValues[$parameter->name]);
							}
							else
							{
								// No value matching this parameter
								$methodArguments[$index] = null;
							}
						}

						// Calling $controller->method with arguments in right order
						call_user_func_array(array($controller, $route->method), $methodArguments);
						$hasRoute = true;
					}
					else
					{
						// Method not found
					}
				}
				else
				{
					// Controller not found
				}
			}
		}

		// No route matched
		if ( !$hasRoute )
		{
			$this->triggerError(404);
		}
	}

	

	private function triggerError($errorCode) 
	{
		switch ( $errorCode )
		{
			case 404:
				$message = 'HTTP/1.0 404 Not Found';
			break;

			default:
				$message = false;
			break;
		}

		if ( $message !== false )
		{
			header($message);
			die();
		}
	}
}

class RequestRoute
{
	
	public $parametersValues;
	public $isMatched;
	public $controller;
	public $method;
	
	private $url;
	private $parametersDefinitions;

	const PARAMETER_TYPE_ALPHA	= '[\d\w\s_-]+';
	const PARAMETER_TYPE_NUMBER	= '\d+';

	const ROUTE_DEFAULT_CONTROLLER	= 'home';
	const ROUTE_DEFAULT_METHOD	= 'index';

	function __construct($url, $requestUri, $routeTarget, $parametersDefinitions)
	{
		$this->isMatched 				= false;
		$this->url 						= $url;
		$this->parametersDefinitions 	= $parametersDefinitions;
		$this->parametersValues			= array();

		if ( ($pos = strpos($requestUri, '?')) !== false )
		{
			$requestUri = substr($requestUri, 0, $pos);
		}

		preg_match_all('|:([\w]+)|', $url, $routeParameters);
		$routeParametersNames = $routeParameters[1];
		
		$patternMatching = $this->url;
		foreach ( $this->parametersDefinitions as $parameterName=>$parameterDefinition )
		{
			$patternMatching = str_replace(':' . $parameterName, '(' . $parameterDefinition . ')', $patternMatching);
		}
	
		// requestUri is matching pattern, set as matched route
		if ( preg_match('|' . $patternMatching . '|', $requestUri, $matching) )
		{
			unset($matching[0]);
			$this->parametersValues = array_combine($routeParametersNames, $matching);
			$this->controller 		= isset($routeTarget['controller']) ? $routeTarget['controller'] : self::ROUTE_DEFAULT_CONTROLLER; 
			$this->method 			= isset($routeTarget['method']) ? $routeTarget['method'] : self::ROUTE_DEFAULT_METHOD;
			
			$this->isMatched 		= true;
		}
	}

}

?>