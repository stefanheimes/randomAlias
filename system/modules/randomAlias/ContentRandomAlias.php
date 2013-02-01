<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    randomAlias
 * @license    GNU/LGPL
 * @filesource
 */

class ContentRandomAlias extends ContentElement
{

    /**
     * Parse the template
     * @return string
     */
    public function generate()
    {
        $arrElements = deserialize($this->randomAlias);
        $intCount    = count($arrElements);

        // Check if we have elements
        if (empty($arrElements))
        {
            return '';
        }

        // Get all element types
        $arrRandomElements = array();
        $arrStayElements = array();
        $arrShuffelElements = array();

        foreach ((array) $arrElements as $key => $value)
        {
            if ($value['id'] == $this->id)
            {
                continue;
            }

            if ($value['type'] == 'random')
            {
                $arrRandomElements[$key] = $value;
            }
            else if ($value['type'] == 'stand')
            {
                $arrStayElements[$key] = $value;
            }
        }

        // Build new array
        for ($i = 0; $i < $intCount; $i++)
        {
            if (key_exists($i, $arrStayElements))
            {
                $arrShuffelElements[$i] = $arrStayElements[$i];
            }
            else
            {
                $arrShuffelElements[$i] = $this->getRaondomItem($arrRandomElements);
            }
        }

        // Get content 
        $strReturn = '';

        foreach ($arrShuffelElements as $key => $value)
        {
            $objElement = $this->Database->prepare("SELECT * FROM tl_content WHERE id=?")
                    ->limit(1)
                    ->execute($value['article']);

            if ($objElement->numRows < 1)
            {
                continue;
            }

            $strClass = $this->findContentElement($objElement->type);

            if (!$this->classFileExists($strClass))
            {
                continue;
            }

            $objElement->id         = $this->id;
            $objElement->typePrefix = 'ce_';

            $objElement = new $strClass($objElement);

            // Overwrite spacing and CSS ID
            $objElement->space = $this->space;
            $objElement->cssID = $this->cssID;

            $strReturn .= $objElement->generate();
        }

        return $strReturn;
    }

    /**
     * Generate the content element
     */
    protected function compile()
    {
        return;
    }

    /**
     * Get a random element
     * 
     * @param array $arrElements
     * @return mixed
     */
    protected function getRaondomItem(&$arrElements)
    {
        $mixKey = array_rand($arrElements, 1);

        $mixReturn = $arrElements[$mixKey];
        unset($arrElements[$mixKey]);

        return $mixReturn;
    }

}

?>