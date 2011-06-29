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
 * @package    SelectionProcess_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "selection-process";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `SelectionProcess` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `user_id` bigint(20) NOT NULL,
                  `classroom_id` bigint(20) NOT NULL,
                  `question` text NOT NULL,
                  `answer` text NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `classroom_id` (`classroom_id`)
                )";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addAdminMenuItem('selection-process', 'processes','selection-process/index/index');
        
        $this->_addAclItem('selection-process/index/index', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/form', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/save', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/list-classroom', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/add-course', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/remove-course', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/list-pre-registration', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/pre-register', 'identified');
        $this->_addAclItem('selection-process/index/matriculate', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/reject', 'teacher, coordinator, institution');
        $this->_addAclItem('selection-process/index/view', 'identified');
    }

    public function desactivate()
    {
        $this->_removeAdminMenuItem('selection-process', 'processes');
        
        $this->_removeAclItem('selection-process/index/index');
        $this->_removeAclItem('selection-process/index/form');
        $this->_removeAclItem('selection-process/index/save');
        $this->_removeAclItem('selection-process/index/list-classroom');
        $this->_removeAclItem('selection-process/index/add-course');
        $this->_removeAclItem('selection-process/index/remove-course');
        $this->_removeAclItem('selection-process/index/list-pre-registration');
        $this->_removeAclItem('selection-process/index/pre-register');
        $this->_removeAclItem('selection-process/index/matriculate');
        $this->_removeAclItem('selection-process/index/reject');
        $this->_removeAclItem('selection-process/index/view');
    }
}

