<?php
// $Header

/**
 * Classes and functions for the template engines.
 *
 * @author The phpLDAPadmin development team
 * @package phpLDAPadmin
 */

/**/
# To make it easier to debug this script, define these constants, which will add some __METHOD__ location displays to the rendered text.
define('DEBUGTMP',0);
define('DEBUGTMPSUB',0);

/**
 * Abstract Visitor class
 *
 * @package phpLDAPadmin
 * @subpackage Templates
 */
abstract class Visitor {
	# The server that was used to configure the templates
	protected $server_id;

	public function __call($method,$args) {
		if (! in_array($method,array('get','visit','draw')))
			debug_dump_backtrace(sprintf('Incorrect use of method loading [%s]',$method),1);

		$methods = array();

		$fnct = array_shift($args);

		$object = $args[0];
		$class = get_class($object);

		$call = "$method$fnct$class";

		array_push($methods,$call);

		while ($class && ! method_exists($this,$call)) {
			if (defined('DEBUGTMP') && DEBUGTMP)
				printf('<font size=-2><i>Class (%s): Method doesnt exist (%s,%s)</i></font><br />',$class,get_class($this),$call);

			$class = get_parent_class($class);
			$call = "$method$fnct$class";
			array_push($methods,$call);
		}

		if (defined('DEBUGTMP') && DEBUGTMP)
			printf('<font size=-2><i>Calling Methods: %s</i></font><br />',implode('|',$methods));

		if (defined('DEBUGTMP') && DEBUGTMP && method_exists($this,$call))
			printf('<font size=-2>Method Exists: %s::%s (%s)</font><br />',get_class($this),$call,$args);

		if (method_exists($this,$call)) {
			$call .= '(';

			for ($i = 0; $i < count($args); $i++)
				if ($i == 0)
					$call .= sprintf('$args[%s]',$i);
				else
					$call .= sprintf(',$args[%s]',$i);

			$call .= ');';

			if (defined('DEBUGTMP') && DEBUGTMP)
				printf('<font size=-2><b>Invoking Method: $this->%s</b></font><br />',$call);

			eval('$r = $this->'.$call);

			if (isset($r))
				return $r;
			else
				return;

		} elseif (DEBUG_ENABLED) {
			debug_log('Doesnt exist param (%s,%s)',1,__FILE__,__LINE__,__METHOD__,$method,$fnct);
		}

		printf('<font size=-2><i>NO Methods: %s</i></font><br />',implode('|',$methods));
	}

	/**
	 * Return the LDAP server ID
	 *
	 * @return int Server ID
	 */
	public function getServerID() {
		if (isset($this->server_id))
			return $this->server_id;
		else
			return null;
	}

	/**
	 * Return this LDAP Server object
	 *
	 * @return object DataStore Server
	 */
	protected function getServer() {
		return $_SESSION[APPCONFIG]->getServer($this->getServerID());
	}
}
?>
