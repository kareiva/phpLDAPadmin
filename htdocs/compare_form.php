<?php
// $Header$

/**
 * Compares two DN entries side by side.
 * This is the entry form to determine which DN to compare this DN with.
 *
 * @package phpLDAPadmin
 * @subpackage Page
 */

/**
 */

require './common.php';

# The DN we are working with
$request = array();
$request['dn'] = get_request('dn','GET');

# Check if the entry exists.
if (! $request['dn'] || ! $app['server']->dnExists($request['dn']))
	error(sprintf(_('The entry (%s) does not exist.'),$request['dn']),'error','index.php');

$request['page'] = new PageRender($app['server']->getIndex(),get_request('template','REQUEST',false,'none'));
$request['page']->setDN($request['dn']);
$request['page']->accept();

# Render the form
$request['page']->drawTitle(sprintf('%s <b>%s</b>',_('Compare another DN with'),get_rdn($request['dn'])));
$request['page']->drawSubTitle();

echo '<center>';
printf('%s <b>%s</b> %s<br />',_('Compare'),get_rdn($request['dn']),_('with '));

echo '<form action="cmd.php" method="post" name="compare_form">';
echo '<input type="hidden" name="cmd" value="compare" />';
printf('<input type="hidden" name="server_id" value="%s" />',$app['server']->getIndex());
printf('<input type="hidden" name="server_id_src" value="%s" />',$app['server']->getIndex());
printf('<input type="hidden" name="dn_src" value="%s" />',htmlspecialchars($request['dn']));
echo "\n";

echo '<table style="border-spacing: 10px">';

echo '<tr>';
printf('<td><acronym title="%s">%s</acronym>:</td>',
	_('Compare this DN with another'),_('Destination DN'));
echo '<td>';
echo '<input type="text" name="dn_dst" size="45" value="" />';
draw_chooser_link('compare_form.dn_dst','true','');
echo '</td>';
echo '</tr>';
echo "\n";

printf('<tr><td>%s:</td><td>%s</td></tr>',_('Destination Server'),server_select_list($app['server']->getIndex(),true,'server_id_dst'));
echo "\n";

printf('<tr><td colspan="2" align="right"><input type="submit" value="%s" /></td></tr>',_('Compare'));
echo "\n";

echo '</table>';
echo '</form>';
echo '</center>';
?>
