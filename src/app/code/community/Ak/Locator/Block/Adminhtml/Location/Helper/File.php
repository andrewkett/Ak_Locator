<?php
class Ak_Locator_Block_Adminhtml_Location_Helper_File extends Varien_Data_Form_Element_Abstract
{
    /**
     * Initialize Form Element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('file');
    }

    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-file');
        if ($this->getRequired()) {
            $this->removeClass('required-entry');
            $this->addClass('required-file');
        }

        $element = sprintf(
            '<input id="%s" name="%s" %s />%s%s',
            $this->getHtmlId(),
            $this->getName(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getAfterElementHtml(),
            $this->_getHiddenInput()
        );

        return $this->_getPreviewHtml() . $element . $this->_getDeleteCheckboxHtml();
    }

    /**
     * Return Delete File CheckBox HTML
     *
     * @return string
     */
    protected function _getDeleteCheckboxHtml()
    {
        $html = '';
        if ($this->getValue() && !$this->getRequired() && !is_array($this->getValue())) {
            $checkboxId = sprintf('%s_delete', $this->getHtmlId());
            $checkbox   = array(
                'type'  => 'checkbox',
                'name'  => sprintf('%s[delete]', $this->getName()),
                'value' => '1',
                'class' => 'checkbox',
                'id'    => $checkboxId
            );
            $label      = array(
                'for'   => $checkboxId
            );
            if ($this->getDisabled()) {
                $checkbox['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }

            $html .= '<span class="' . $this->_getDeleteCheckboxSpanClass() . '">';
            $html .= $this->_drawElementHtml('input', $checkbox) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false) . $this->_getDeleteCheckboxLabel() . '</label>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-file';
    }

    /**
     * Return Delete CheckBox Label
     *
     * @return string
     */
    protected function _getDeleteCheckboxLabel()
    {
        return Mage::helper('adminhtml')->__('Delete File');
    }

    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $image = array(
                'alt'   => Mage::helper('adminhtml')->__('Download'),
                'title' => Mage::helper('adminhtml')->__('Download'),
                'src'   => Mage::getDesign()->getSkinUrl('images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            );
            $url = $this->_getPreviewUrl();
            $html .= '<span>';
            $html .= '<a href="' . $url . '">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $html .= '<a href="' . $url . '">' . Mage::helper('adminhtml')->__('Download') . '</a>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Hidden element with current value
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return $this->_drawElementHtml('input', array(
            'type'  => 'hidden',
            'name'  => sprintf('%s[value]', $this->getName()),
            'id'    => sprintf('%s_value', $this->getHtmlId()),
            'value' => $this->getEscapedValue()
        ));
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/locator/viewfile', array(
            'file'      => Mage::helper('core')->urlEncode($this->getValue()),
        ));
    }

    /**
     * Return Element HTML
     *
     * @param string $element
     * @param array $attributes
     * @param boolean $closed
     * @return string
     */
    protected function _drawElementHtml($element, array $attributes, $closed = true)
    {
        $parts = array();
        foreach ($attributes as $k => $v) {
            $parts[] = sprintf('%s="%s"', $k, $v);
        }

        return sprintf('<%s %s%s>', $element, implode(' ', $parts), $closed ? ' /' : '');
    }

    /**
     * Return escaped value
     *
     * @param int $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        if (is_array($this->getValue())) {
            return false;
        }
        $value = $this->getValue();
        if (is_array($value) && is_null($index)) {
            $index = 'value';
        }

        return parent::getEscapedValue($index);
    }
}
