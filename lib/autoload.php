<?

namespace hatchet;

/**
 * Autoloads classes by the namespace
 *
 * @param string $class_name
 * @return bool
 */
function autoload($class_name)
{
	$ns = __NAMESPACE__.'\\';
	if(substr($class_name, 0, strlen($ns)) != $ns)
		return false;

	// hatchet\ns_2\Class_Name_Path => lib/ns_2/Class/Name/Path.php
	$class_name = substr($class_name, strlen($ns));

	$parts = explode('\\', $class_name);
	$class = array_pop($parts);
	$class = strtr($class, array('_'=>'/'));
	$parts[] = $class;

	$class_file = __DIR__ . '/' . join('/', $parts) . '.php';
	if(is_readable($class_file))
	{
		include $class_file;
		return true;
	}
	return false;
}

spl_autoload_register(__NAMESPACE__.'\autoload');
