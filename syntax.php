<?php
/**
 * PHP Markdown Extra plugin for DokuWiki.
 *
 * @license GPL 3 (http://www.gnu.org/licenses/gpl.html) - NOTE: PHP Markdown
 * Extra is licensed under the BSD license. See License.text for details.
 * @version 1.03 - 24.11.2012 - PHP Markdown Extra 1.2.5 included.
 * @author Joonas Pulakka <joonas.pulakka@iki.fi>, Jiang Le <smartynaoki@gmail.com>
 */

if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'syntax.php');
require_once (DOKU_PLUGIN . 'markdownextra/markdown.php');

class syntax_plugin_markdownextra extends DokuWiki_Syntax_Plugin {

    function getType() {
        return 'protected';
    }

    function getPType() {
        return 'block';
    }

    function getSort() {
        return 69;
    }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern('<markdown>(?=.*</markdown>)', $mode, 'plugin_markdownextra');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('</markdown>', 'plugin_markdownextra');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER :      return array($state, '');
            case DOKU_LEXER_UNMATCHED :  return array($state, Markdown($match));
            case DOKU_LEXER_EXIT :       return array($state, '');
        }
        return array($state,'');
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        //dbg('function render($mode, &$renderer, $data)-->'.' mode = '.$mode.' data = '.$data);
        //dbg($data);
        if ($mode == 'xhtml') {
            list($state,$match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :      break;    
                case DOKU_LEXER_UNMATCHED :
                    $match = $this->_toc($renderer, $match);
                    $renderer->doc .= $match;
                    break;
                case DOKU_LEXER_EXIT :       break;
            }
            return true;
        }else if ($mode == 'metadata') {
            //dbg('function render($mode, &$renderer, $data)-->'.' mode = '.$mode.' data = '.$data);
            //dbg($data);
            list($state,$match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :      break;    
                case DOKU_LEXER_UNMATCHED :
                    if (!$renderer->meta['title']){
                        $renderer->meta['title'] = $this->_markdown_header($match);
                    }
                    $this->_toc($renderer, $match);
                    $internallinks = $this->_internallinks($match);
                    #dbg($internallinks);
                    if (count($internallinks)>0){
                        foreach($internallinks as $internallink)
                        {
                            $renderer->internallink($internallink);
                        }
                    }
                    break;
                case DOKU_LEXER_EXIT :       break;
            }
            return true;
        } else {
            return false;
        }
    }

    function _markdown_header($text)
    {   
        $doc = new DOMDocument('1.0','UTF-8');
        //dbg($doc);
        $meta = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
        $doc->loadHTML($meta.$text);
        //dbg($doc->saveHTML());
        if ($nodes = $doc->getElementsByTagName('h1')){
            return $nodes->item(0)->nodeValue;
        }
        return false;
    }
    
    function _internallinks($text)
    {
        $links = array();
        if ( ! $text ) return $links;
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML($text);
        if ($nodes = $doc->getElementsByTagName('a')){
            foreach($nodes as $atag)
            {
                $href = $atag->getAttribute('href');
                if (!preg_match('/^(https{0,1}:\/\/|ftp:\/\/|mailto:)/i',$href)){
                    $links[] = $href;
                }
            }
        }
        return $links;
    }
    
    function _toc(&$renderer, $text)
    {
        $doc = new DOMDocument('1.0','UTF-8');
        //dbg($doc);
        $meta = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
        $doc->loadHTML($meta.$text);
        if ($nodes = $doc->getElementsByTagName("*")){
            foreach($nodes as $node)
            {
                if (preg_match('/h([1-7])/',$node->tagName,$match))
                {
                    #dbg($node);
                    $node->setAttribute('class', 'sectionedit'.$match[1]);
                    $hid = $renderer->_headerToLink($node->nodeValue,'true');
                    $node->setAttribute('id',$hid);
                    $renderer->toc_additem($hid, $node->nodeValue, $match[1]);
                }
                
            }
        }
        //remove outer tags of content
        $html = $doc->saveHTML();
        $html = str_replace('<!DOCTYPE html>','',$html);
        $html = preg_replace('/.+<body>/', '', $html);
        $html = preg_replace('@</body>.*</html>@','', $html);
        return $html;
    }

}