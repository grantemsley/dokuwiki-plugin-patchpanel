<?php
/*
 * Patch Panel Plugin: display a patch panel from a plaintext source
 * 
 * Each patch panel is enclosed in <patchpanel>...</patchpanel> tags. The tag can have the
 * following parameters (all optional):
 *   name=<name>		The name of the patch panel (default: 'Patch Panel')
 *   ports=<number>		The total number of ports.  (default: 48)
 *   rows=<number>		Number of rows.  (default: 2)
 * Between these tags is a series of lines, each describing a port:
 * 
 *		<port> <label> [#color] [comment]
 * 
 * The fields:
 *  - <port>: The port number on the patch panel, starting from the top left.
 *  - <label>: The label for the port.  Must be quoted if it contains spaces.
 *  - [#color]: Optional.  Specify an #RRGGBB HTML color code.
 *  - [comment]: Optional. All remaining text is treated as a comment.  
 *
 * You can also include comment lines starting with a pound sign #.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Grant Emsley <grant@emsley.ca>
 * @version    2014.05.11.1
 *
 * Based on the rack plugin (https://www.dokuwiki.org/plugin:rack) by Tyler Bletsch <tyler.bletsch@netapp.com>
 * 
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/*
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_patchpanel extends DokuWiki_Syntax_Plugin {


	/*
	 * What kind of syntax are we?
	 */
	function getType(){
		return 'substition';
	}

	/*
	 * Where to sort in?
	 */
	function getSort(){
		return 155;
	}

	/*
	 * Paragraph Type
	 */
	function getPType(){
		return 'block';
	}

	/*
	 * Connect pattern to lexer
	 */
	function connectTo($mode) {
		$this->Lexer->addSpecialPattern("<patchpanel[^>]*>.*?(?:<\/patchpanel>)",$mode,'plugin_patchpanel');
	}


	/*
	 * Handle the matches
	 */
	function handle($match, $state, $pos, &$handler){
	
		// remove "</patchpanel>" from the match
		$match = substr($match,0,-13);

		//default options
		$opt = array(
			'name' => 'Patch Panel',
			'ports' => 42,
			'rows' => '2'
		);

		list($optstr,$opt['content']) = explode('>',$match,2);
		unset($match);
		// parse options
		$optsin = explode(' ',$optstr);
		//$optsin = str_getcsv($optstr, ' ');
		
		// http://stackoverflow.com/questions/2202435/php-explode-the-string-but-treat-words-in-quotes-as-a-single-word
		preg_match_all('/\w*?="(?:\\.|[^\\"])*"|\S+/', $optstr, $matches);
		
		$optsin = $matches[0];
		foreach($optsin as $o){
			$o = trim($o);
			if (preg_match("/^name=(.+)/",$o,$matches)) {
				$opt['name'] = str_replace('"',"", $matches[1]);
			} elseif (preg_match("/^ports=(\d+)/",$o,$matches)) {
				$opt['ports'] = $matches[1];
			} elseif (preg_match("/^rows=(\d+)/",$o,$matches)) {
				$opt['rows'] = $matches[1];
			}
		}
		return $opt;
	}

	function autoselect_color($item) {
		$color = '#888';
		if (preg_match('/(wire|cable)\s*guide|pdu|patch|term server|lcd/i',$item['model'])) { $color = '#bba'; }
		if (preg_match('/blank/i',                                         $item['model'])) { $color = '#fff'; }
		if (preg_match('/netapp|fas\d/i',                                  $item['model'])) { $color = '#07c'; }
		if (preg_match('/^Sh(elf)?\s/i',                                   $item['model'])) { $color = '#0AE'; }
		if (preg_match('/cisco|catalyst|nexus/i',                          $item['model'])) { $color = '#F80'; }
		if (preg_match('/brocade|mds/i',                                   $item['model'])) { $color = '#8F0'; }
		if (preg_match('/ucs/i',                                           $item['model'])) { $color = '#c00'; }
		if (preg_match('/ibm/i',                                           $item['model'])) { $color = '#67A'; }
		if (preg_match('/hp/i',                                            $item['model'])) { $color = '#A67'; }
		if (!$item['model']) { $color = '#FFF'; }
		return $color;
	}
	
	
	// Modify an SVG image of an ethernet port
	function ethernet_svg($port, $label, $color, $caption) {
		# Ethernet port
		$image = <<<EOF
<svg xmlns="http://www.w3.org/2000/svg" width="40" height="34" viewbox="0 0 200 170" preserveAspectRatio="xMinYMin meet" class="ethernet">
<metadata id="metadata6">image/svg+xml</metadata>
<g>
	<g id="svg_1">
		<rect fill="#ccc" stroke-width="0" stroke-miterlimit="4" y="-0.783784" x="0" height="170" width="200" id="rect2220" class="outer" />
		<g id="g2242">
			<rect fill="#000000" stroke-width="0" stroke-miterlimit="4" id="rect2228" width="150" height="90" x="25" y="29.162162"/>
			<rect fill="#000000" stroke-width="0" stroke-miterlimit="4" id="rect2230" width="80" height="16" x="60" y="118.162162"/>
			<rect fill="#000000" stroke-width="0" stroke-miterlimit="4" id="rect2232" width="50" height="16" x="75" y="133.162162"/>
		</g>
		<g id="g2263">
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2247" width="6" height="18" x="55" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2249" width="6" height="18" x="67" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2251" width="6" height="18" x="79" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2253" width="6" height="18" x="91" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2255" width="6" height="18" x="103" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2257" width="6" height="18" x="115" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2259" width="6" height="18" x="127" y="31.162162"/>
			<rect fill="#ffff00" stroke-width="0" stroke-miterlimit="4" id="rect2261" width="6" height="18" x="139" y="31.162162"/>
		</g>
	</g>
</g>
</svg>
EOF;
		// Replace color
		if(!substr($color,0,1) == "#") { $color = '#CCCCCC'; }
		$image = str_replace("#REPLACECOLOR#", $color, $image);
		
		// Replace hover text
		$image = str_replace("#REPLACECAPTION#", $caption, $image);
		
		return $image;
	}
	

	/*
	 * Create output
	 */
	function render($mode, &$renderer, $opt) {
		if($mode == 'metadata') return false;

		$content = $opt['content'];
		// clear any trailing or leading empty lines from the data set
		$content = preg_replace("/[\r\n]*$/","",$content);
		$content = preg_replace("/^\s*[\r\n]*/","",$content);

		if(!trim($content)){
			$renderer->cdata('No data found');
		}
		
		$items = array();
		
		$csv_id = uniqid("csv_");
		$csv = "Port,Label,Comment\n";
		
		foreach (explode("\n",$content) as $line) {
			$item = array();
			if (!preg_match("/^\s*\d+/",$line)) { continue; } # skip lines that don't start with a port number
			
			# split on whitespace, keep quoted strings together
			$matchcount = preg_match_all('/"(?:\\.|[^\\"])*"|\S+/',$line,$matches);
			if ($matchcount > 0) {
				$item['port'] = $matches[0][0];
				$item['label'] = $matches[0][1];
				# If 3rd element starts with #, it's a color.  Otherwise part of the comment
				if (substr($matches[0][2], 0, 1) == "#") {
					$item['color'] = $matches[0][2];
				} else {
					$item['comment'] = $matches[0][2];
				}
				# Any remaining text is part of the comment.
				for($x=3;$x<=$matchcount;$x++) {
					$item['comment'] .= " ".$matches[0][$x];
				}
				$items[$item['port']] = $item;
				$csv .= "\"$item[port]\",\"$item[label]\",\"$item[comment]\"\n";
			} else {
				$renderer->doc .= 'Syntax error on the following line: <pre style="color:red">'.hsc($line)."</pre>\n";
			}
		}
		



		$renderer->doc .= '<div class="patchpanel" style="content: '.'; display:block;height0;overflow:visible; visibility: visible;">';
		$renderer->doc .= "<table class='patchpanel'>";
		
		$portsPerRow = ceil($opt['ports']/$opt['rows']);
		for ($row=1; $row <= $opt['rows']; $row++) {
		
			// Calculate the starting and ending ports for this row.
			$startPort = 1+$portsPerRow*($row-1);
			$endPort = $portsPerRow+$portsPerRow*($row-1);
			if ($endPort > $opt['ports']) { $endPort = $opt['ports']; }
		
			$renderer->doc .= "<tr class='patchpanel_labels'>";
			for ($port=$startPort; $port <= $endPort ; $port++) {
				$renderer->doc .= "<td>" . $items[$port]['label'] . "</td>";
			}
			$renderer->doc .= "</tr>";
			
			$renderer->doc .= "<tr class='patchpanel_ports'>";
			for ($port=$startPort; $port <= $endPort ; $port++) {

				$renderer->doc .= "<td>" . $this->ethernet_svg($port,$items[$port]['label'],$items[$port]['color'],$items[$port]['caption']) . "</td>";
			}
			$renderer->doc .= "</tr>";
			
			$renderer->doc .= "<tr class='patchpanel_numbers'>";
			for ($port=$startPort; $port <= $endPort ; $port++) {
				$renderer->doc .= "<td>" . $port . "</td>";
			}
			$renderer->doc .= "</tr>";

			$renderer->doc .= "<tr class='patchpanel_blank'>";
			for ($port=$startPort; $port <= $endPort ; $port++) {
				$renderer->doc .= "<td>&nbsp;</td>";
			}
			$renderer->doc .= "</tr>";




			
		}
		$renderer->doc .= "</table>";
		$renderer->doc .= "</div>";

		
		
		
		
//		$u_first = $opt['descending'] ? 1 : $opt['height'];
//		$u_last  = $opt['descending'] ? $opt['height'] : 1; 
//		$u_delta = $opt['descending'] ? +1 : -1;
//		$renderer->doc .= "<table class='rack'><tr><th colspan='2' class='title'>$opt[name]</th></tr>\n";
//		#for ($u=$opt['height']; $u>=1; $u--) {
//		#foreach (range($u_first,$u_last,$u_delta) as $u) {
//		for ($u=$u_first;  ($opt['descending'] ? $u<=$u_last : $u>=$u_last);  $u += $u_delta) {
//			if ($items[$u] && $items[$u]['model']) {	
//				$item = $items[$u];
//				$renderer->doc .= 
//					"<tr><th>$u</th>".
//					"<td class='item' rowspan='$item[u_size]' style='background-color: $item[color];' title=\"".htmlspecialchars($item['comment'])."\">".
//					($item['link'] ? '<a href="'.$item['link'].'"'.$item['linktitle'].'>' : '').
//					"<div style='float: left; font-weight:bold;'>".
//						"$item[model]" .
//						($item['comment'] ? ' *' : '').
//					"</div>".
//					"<div style='float: right; margin-left: 3em; '>$item[name]".
//					"</div>".
//					($item['link'] ? '</a>' : '').
//					"</td></tr>\n";
//				for ($d = 1; $d < $item['u_size']; $d++) {
//					$u += $u_delta;
//					$renderer->doc .= "<tr><th>$u</th></tr>\n";
//				}
//			} else {
//				$renderer->doc .= "<Tr><Th>$u</th><td class='empty'></td></tr>\n";
//			}
//		}
//		# we use a whole row as a bottom border to sidestep an asinine rendering bug in firefox 3.0.10
//		$renderer->doc .= "<tr><th colspan='2' class='bottom'><span style='cursor: pointer;' onclick=\"this.innerHTML = rack_toggle_vis(document.getElementById('$csv_id'),'block')?'Hide CSV &uarr;':'Show CSV &darr;';\">Show CSV &darr;</span></th></tr>\n";
//		$renderer->doc .= "</table>&nbsp;";
//		
//		# this javascript hack sets the CSS "display" property of the tables to "inline", 
//		# since IE is too dumb to have heard of the "inline-table" mode.
//		$renderer->doc .= "<script type='text/javascript'>rack_ie6fix();</script>\n";
//
//		$renderer->doc .= "<pre style='display:none;' id='$csv_id'>$csv</pre>\n";
		
		return true;
	}
	
}
