<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @category   SelectionProcess
 * @package    SelectionProcess_Form
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_Form_PreRegister extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table = new Tri_Db_Table('selection_process');

		$this->addElementPrefixPath('Tri_Filter', 'Tri/Filter', 'FILTER');

        $validators = $table->getValidators();
        $filters    = $table->getFilters();

        $this->setAction('selection-process/index/sign')
             ->setMethod('post');
		
		$classroom_id = new Zend_Form_Element_Hidden('classroom_id');
        $classroom_id->addValidators($validators['classroom_id'])
			         ->addFilters($filters['classroom_id'])
			         ->removeDecorator('Label')
			         ->removeDecorator('HtmlTag');
		
        $filters['justify'][] = 'StripTags';
        $justify = new Zend_Form_Element_Textarea('justify');
        $justify->setLabel('cause course')
                ->addValidators($validators['justify'])
                ->addFilters($filters['justify'])
                ->setAttrib('id', 'justify-text')
				->setAttrib('style', 'height:100px;')
                ->setAllowEmpty(false);		

        $this->addElement($classroom_id)
			 ->addElement($justify)
             ->addElement('submit', 'Save');
   }
}
