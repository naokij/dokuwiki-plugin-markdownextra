<?php
/**
 * DokuWiki Plugin markdownextra (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'action.php';

class action_plugin_markdownextra extends DokuWiki_Action_Plugin {

   function register(Doku_Event_Handler $controller) {
      $controller->register_hook('PARSER_WIKITEXT_PREPROCESS',
'BEFORE', $this, 'handle_parser_wikitext_preprocess');
      $controller->register_hook('TPL_METAHEADER_OUTPUT',
'BEFORE', $this, 'handle_meltdown_metadata');
   }

   function handle_parser_wikitext_preprocess(&$event, $param) {
       global $ACT;
       global $ID;
       global $TEXT;
       // Check if file is a .md page:
       if(substr($ID,-3) != '.md') return true;
       // Check for default view (in this case there is only 1 parsed text)
       // or check that the text parsed is the text being edited
       // (see: http://www.dokuwiki.org/devel:environment#text):
       if($ACT != 'show' && $event->data != $TEXT) return true;
       
       if ($this->getConf('frontmatter')){
           if (preg_match('/^---\s*\n(.*?\n?)^---\s*$\n?(.+)/sm',$event->data, $match)){
               $event->data = sprintf("%s<markdown>\n%s\n</markdown>", $match[1], $match[2]);
           }else{
               $event->data = "<markdown>\n".$event->data."\n</markdown>";
           }
       }else{
           $event->data = "<markdown>\n".$event->data."\n</markdown>";
       }
   }

   function handle_meltdown_metadata(&$event, $param) {
       global $ACT;
       global $ID;
       // Check if file is a .md page and if we are editing a page:
       if (substr($ID,-3) != '.md' || $ACT != 'edit') return;
       
       if ($this->getConf('markdowneditor') == 'meltdown') {
           $meltdownBase = DOKU_BASE.'lib/plugins/markdownextra/lib/meltdown/';
           $meltdownTweaksBase = DOKU_BASE.'lib/plugins/markdownextra/lib/meltdown-tweaks/';
           // Add Meltdown css and script files, as well as our custom css and js tweaks:
           $event->data['link'][] = array(
                'rel'     => 'stylesheet',
                'type'    => 'text/css',
                'href'    => $meltdownBase.'css/meltdown.css');
           $event->data['link'][] = array(
                'rel'     => 'stylesheet',
                'type'    => 'text/css',
                'href'    => $meltdownTweaksBase.'meltdown-tweaks.css');
           $event->data['script'][] = array(
                'type'    => 'text/javascript',
                '_data'   => '',
                'src'     => $meltdownBase.'js/jquery.meltdown.js');
           $event->data['script'][] = array(
                'type'    => 'text/javascript',
                '_data'   => '',
                'src'     => $meltdownBase.'js/lib/js-markdown-extra.js');
           $event->data['script'][] = array(
                'type'    => 'text/javascript',
                '_data'   => '',
                'src'     => $meltdownBase.'js/lib/rangyinputs-jquery.min.js');
           $event->data['script'][] = array(
                'type'    => 'text/javascript',
                '_data'   => '',
                'src'     => $meltdownTweaksBase.'meltdown-tweaks.js');
        }
   }
}