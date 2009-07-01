<?php
// $Header$

/**
 * Fetches and displays all information that it can from the specified server
 *
 * @package phpLDAPadmin
 * @subpackage Page
 */

/**
 */

require './common.php';

# Fetch basic RootDSE attributes using the + and *.
$query = array();
$query['base'] = '';
$query['scope'] = 'base';
$query['attrs'] = $app['server']->getValue('server','root_dse_attributes');
$query['baseok'] = true;
$results = $app['server']->query($query,null);
$attrs = array_pop($results);

printf('<h3 class="title">%s%s</h3>',_('Server info for: '),$app['server']->getName());
printf('<h3 class="subtitle">%s</h3>',_('Server reports the following information about itself'));

if (! count($attrs)) {
	echo '<br /><br />';
	printf('<center>%s</center>',_('This server has nothing to report.'));
	return;
}

echo '<table class="result" border=0>';
foreach ($attrs as $key => $values) {
	if ($key == 'dn')
		continue;

	$href = sprintf('cmd.php?cmd=schema&amp;server_id=%s&amp;view=attributes&amp;viewvalue=%s',$app['server']->getIndex(),$key);

	echo '<tr class="list_item"><td class="heading" rowspan=2>';
	printf('<a href="%s" title="%s: %s" >%s</a>',
		$href,_('Click to view the schema definition for attribute type'),$key,$key);
	echo '</td></tr>';

	echo '<tr class="list_item"><td class="blank">&nbsp;</td><td class="value">';
	echo '<table class="result" border=0>';

	if (is_array($values))
		foreach ($values as $value) {
			$oidtext = '';
			print '<tr>';

			if (preg_match('/^[0-9]+\.[0-9]+/',$value)) {
				printf('<td width=5%% rowspan=2 style="vertical-align: top"><img src="%s/rfc.png" title="%s" alt="%s"/></td>',
					IMGDIR,$value,htmlspecialchars($value));

				if ($oidtext = support_oid_to_text($value))
					if (isset($oidtext['ref']))
						printf('<td><acronym title="%s">%s</acronym></td>',$oidtext['ref'],$oidtext['title']);
					else
						printf('<td>%s</td>',$oidtext['title']);

				else
					if (strlen($value) > 0)
						printf('<td><small>%s</small></td>',$value);

			} else {
				printf('<td rowspan=2 colspan=2>%s</td>',$value);
			}

			print '</tr>';

			if (isset($oidtext['desc']) && trim($oidtext['desc']))
				printf('<tr><td><small>%s</small></td></tr>',$oidtext['desc']);
			else
				echo '<tr><td>&nbsp;</td></tr>';

			if ($oidtext)
				echo '<tr><td colspan=2>&nbsp;</td></tr>';
		}

	else
		printf('<tr><td colspan=2>%s&nbsp;</td></tr>',$values);


	echo '</table>';
	echo '</td></tr>';
}
echo '</table>';
?>
