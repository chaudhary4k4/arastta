<?php
/**
 * @package		Arastta Form Component
 * @copyright	Copyright (C) 2015 Arastta Association. All rights reserved. (arastta.org)
 * @copyright	Copyright (C) 2009-2013 Luke Korth
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

namespace Arastta\Component\Form\View;

class SideBySide extends \Arastta\Component\Form\View {
	
	protected $class = "form-horizontal";

	public function render() {
		$this->_form->appendAttribute("class", $this->class);

		echo '<form', $this->_form->getAttributes(), '><fieldset>';

		$this->_form->getErrorView()->render();

		$elements = $this->_form->getElements();
		$elementSize = sizeof($elements);
		$elementCount = 0;

		for ($e = 0; $e < $elementSize; ++$e) {
			$element = $elements[$e];

			if ($element instanceof \Arastta\Component\Form\Element\Hidden || $element instanceof \Arastta\Component\Form\Element\HTML) {
				$element->render();
			} elseif ($element instanceof \Arastta\Component\Form\Element\Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof \Arastta\Component\Form\Element\Button) {
					echo '<div class="form-actions">';
				} else {
					echo ' ';
				}

				$element->render();

                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \Arastta\Component\Form\Element\Button) {
					echo '</div>';
				}
            } else {
				$required = null;

				if($element->isRequired()) {
					$required = 'required';
				}

				echo '<div class="form-group ' . $required . '">', $this->renderLabel($element), '<div class="col-sm-10">', $element->render(), $this->renderDescriptions($element), '</div></div>';

				++$elementCount;
			}
		}

		echo '</fieldset></form>';
    }

	protected function renderLabel(\Arastta\Component\Form\Element $element) {
        $label = $element->getLabel();

        if (!empty($label)) {
			echo '<label class="col-sm-2 control-label" for="', $element->getAttribute("id"), '">';
			echo $label, '</label>'; 
        }
    }
}