<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibApplicationTemplateFilterHtml extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Convert the alias.
     *
     * @param string
     *
     * @return KTemplateFilterAlias
     */
    public function write(&$text)
    {
        $matches = array();

        if (strpos($text, '<html')) {
            //add language
            $text = str_replace('<html', '<html lang="'.JFactory::getLanguage()->getTag().'"', $text);

            //render the styles
            $text = str_replace('</head>', $this->_renderHead().$this->_renderStyles().'</head>', $text);

            //render the scripts
            $text = str_replace('</body>', $this->_renderScripts().'</body>', $text);
        }
    }

    /**
     * Render title.
     *
     * @return string
     */
    protected function _renderHead()
    {
        $document = $this->getService('application.document')->getInstance();
        $html = '<base href="base://" />';
        $html .= '<meta name="description" content="'.$document->getDescription().'" />';
        $html .= '<title>'.$document->getTitle().'</title>';

        return $html;
    }

    /**
     * Return the document scripts.
     *
     * @return string
     */
    protected function _renderScripts()
    {
        $document = $this->getService('application.document')->getInstance();

        $string = '';
        $string .= $this->_template->getHelper('javascript')->language('lib_anahita');

        $scripts = array_reverse($document->getScripts());

        foreach ($scripts as $src => $type) {
            $string .= '<script type="'.$type.'" src="'.$src.'"></script>';
        }

        $script = $document->getScript();

        foreach ($script as $type => $content) {
            $string .= '<script type="'.$type.'">'.$content.'</script>';
        }

        return $string;
    }

    /**
     * Return the document styles.
     *
     * @return string
     */
    protected function _renderStyles()
    {
        $document = $this->getService('application.document')->getInstance();
        $html = '';

        // Generate stylesheet links
        foreach ($document->getStyleSheets() as $src => $attr) {

            $rel = 'stylesheet';

            if (strpos($src, '.less')) {
                $rel .= '/less';
            }

            $html .= '<link rel="'.$rel.'" href="'.$src.'" type="'.$attr['mime'].'"';

            if (isset($attr['media'])) {
                $html .= ' media="'.$attr['media'].'" ';
            }

            if ($temp = JArrayHelper::toString($attr['attribs'])) {
                $html .= ' '.$temp;
            }

            $html .= '/>';
        }

        foreach ($document->getStyle() as $type => $content) {
            $html .= '<style type="'.$type.'">'.$content.'</style>';
        }

        return $html;
    }
}
