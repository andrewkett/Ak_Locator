<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

/**
*
*/
class Ak_Locator_LocationController extends Mage_Core_Controller_Front_Action
{

    /**
     * Location view action
     */
    public function indexAction()
    {
        $this->loadLayout();

        //if there are no matching locations forward to the 404 page
        if (!$this->getLayout()->getBlock('view')->getLocation()){
            $this->_forward('noresults');
            return;
        }

        $this->renderLayout();
    }
}
