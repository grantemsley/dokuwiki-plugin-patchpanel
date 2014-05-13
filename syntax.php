<?php
/*
 * Patch Panel Plugin: display a patch panel from a plaintext source
 * 
 * Each patch panel is enclosed in <patchpanel>...</patchpanel> tags. The tag can have the
 * following parameters (all optional):
 *   name=<name>		The name of the patch panel (default: 'Patch Panel')
 *   ports=<number>		The total number of ports.  (default: 48)
 *   rows=<number>		Number of rows.  (default: 2)
 *   groups=<number>	Number of ports in a group (default: 6)
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
			'rows' => '2',
			'groups' => '6'
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
			} elseif (preg_match("/^groups=(\d+)/",$o,$matches)) {
				$opt['groups'] = $matches[1];
			}
		}
		return $opt;
	}

	
	// This function creates an SVG image of an ethernet port and positions it on the patch panel.
	function ethernet_svg($row, $position, $group, $port, $label, $color, $caption) {
		// Make row and position 0-indexed.
		$row--;
		$position--;
		
		# Ethernet port
		$image = <<<EOF
<svg xmlns="http://www.w3.org/2000/svg" y="#REPLACEY#" x="#REPLACEX#" width="40" height="134" viewbox="0 0 200 270" preserveAspectRatio="xMinYMin meet" class="ethernet">
<metadata id="metadata6">image/svg+xml</metadata>
<g>
  <g>
   <rect width="200" height="100" x="-1" y="0" fill="#fff" stroke-width="5" stroke="#000000" fill="#ffffff" ry="21" rx="21"/>
   <text font-weight="bold" transform="matrix(2.23270613481651,0,0,2.71289621263044,-0.5055757463585078,-84.46232908315777) " xml:space="preserve" text-anchor="middle" font-family="sans-serif" font-size="26" stroke-width="0" stroke="#000000" fill="#000" id="svg_4" y="57.102295" x="42.59375">#REPLACELABEL#</text>
   <g id="svg_1">
    <rect id="rect2220" width="200" height="170" x="-1" y="100.216216" stroke-miterlimit="4" stroke-width="0" fill="#REPLACECOLOR#"/>
    <g id="g2242">
     <rect y="130.162162" x="24" height="90" width="150" id="rect2228" stroke-miterlimit="4" stroke-width="0" fill="#000000"/>
     <rect y="219.162162" x="59" height="16" width="80" id="rect2230" stroke-miterlimit="4" stroke-width="0" fill="#000000"/>
     <rect y="234.162162" x="74" height="16" width="50" id="rect2232" stroke-miterlimit="4" stroke-width="0" fill="#000000"/>
    </g>
    <g id="g2263">
     <rect y="132.162162" x="54" height="18" width="6" id="rect2247" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="66" height="18" width="6" id="rect2249" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="78" height="18" width="6" id="rect2251" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="90" height="18" width="6" id="rect2253" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="102" height="18" width="6" id="rect2255" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="114" height="18" width="6" id="rect2257" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="126" height="18" width="6" id="rect2259" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
     <rect y="132.162162" x="138" height="18" width="6" id="rect2261" stroke-miterlimit="4" stroke-width="0" fill="#ffff00"/>
    </g>
   </g>
	<text transform="matrix(2.0431718826293945,0,0,1.720379114151001,-68.70264820754528,-44.79857616126537) " xml:space="preserve" text-anchor="middle" font-family="sans-serif" font-size="32" stroke-width="0" stroke="#000000" fill="#ffffff" id="svg_2" y="143.102295" x="83.59375">#REPLACEPORTNUMBER#</text>
	
</g>
</svg>
EOF;
		// Replace color
		if(!substr($color,0,1) == "#") { $color = '#CCCCCC'; }
		$image = str_replace("#REPLACECOLOR#", $color, $image);
		
		// Replace label
		$image = str_replace("#REPLACELABEL#", $label, $image);
		
		// Replace hover text
		$image = str_replace("#REPLACECAPTION#", $caption, $image);
		
		// Add port number
		$image = str_replace("#REPLACEPORTNUMBER#", $port, $image);
		
		// Position the port
		$image = str_replace("#REPLACEX#", 100+$position*46+$group*30, $image);
		$image = str_replace("#REPLACEY#", 20+$row*66, $image);
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
		
		$portsPerRow = ceil($opt['ports']/$opt['rows']);
		$groups = ceil($portsPerRow/$opt['groups']);
		$imagewidth = 100+$portsPerRow*46+$groups*30+60;
		$imageheight = 20+$opt['rows']*66;


		// Outer div allows scrolling horizontally
		$renderer->doc .= '<div class="patchpanel" style="display:block;line-height:0;overflow-x: auto; overflow-y: hidden; width:100%; ">';
		$renderer->doc .= "<div style='height:" . $imageheight . "px; width:" . $imagewidth . "px;'>";
		$renderer->doc .= "<svg viewbox='0 0 ".$imagewidth." ".$imageheight."' style='line-height:0px;'>";
		$renderer->doc .=  '<rect stroke-width="5" fill="#000000" height="100%" width="100%" x="0" y="0" rx="30" ry="30" />';
		// Draw some mounting holes
		$renderer->doc .= '<rect fill="#fff" x="20" y="20" width="30" height="17.6" ry="9" />';
		$renderer->doc .= '<rect fill="#fff" x="' . ($imagewidth-20-30) . '" y="20" width="30" height="17.6" ry="9" />';
		$renderer->doc .= '<rect fill="#fff" x="20" y="'. ($imageheight-20-17.6) .'" width="30" height="17.6" ry="9" />';
		$renderer->doc .= '<rect fill="#fff" x="' . ($imagewidth-20-30) . '" y="' . ($imageheight-20-17.6) . '" width="30" height="17.6" ry="9" />';
		// And a label
		$renderer->doc .= '<text transform="rotate(-90 80,' . $imageheight/2 . ') " text-anchor="middle" font-size="12" fill="#fff" y="' . $imageheight/2 . '" x="80">' . $opt['name'] . ' </text>';
		



		for ($row=1; $row <= $opt['rows']; $row++) {
		
			// Calculate the starting and ending ports for this row.
			$startPort = 1+$portsPerRow*($row-1);
			$endPort = $portsPerRow+$portsPerRow*($row-1);
			if ($endPort > $opt['ports']) { $endPort = $opt['ports']; }
			
			// Draw ethernet ports over the patch panel
			for ($port=$startPort; $port <= $endPort ; $port++) {
				$position = $port - $portsPerRow*($row-1);
				$group = floor(($position-1)/$opt['groups']);
				$renderer->doc .= $this->ethernet_svg($row, $position, $group, $port, $items[$port]['label'],$items[$port]['color'],$items[$port]['caption']);
			}




			
		}
		$renderer->doc .= "</svg></div>";
		$renderer->doc .= "</div>";

		
		
		
		
		return true;
	}
	
}
