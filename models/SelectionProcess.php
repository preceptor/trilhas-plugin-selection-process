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
 * @package    SelectionProcess_Model
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_Model_SelectionProcess
{
   /**
    * Status type: waiting
    */
	const WAITING = 'waiting';
	
   /**
    * Status type: accepts
    */
	const ACCEPTS = 'accepts';
	
   /**
    * Status type: rejected
    */	
	const REJECTED = 'rejected';
	
	/**
	 * Verifies that the student has made the pre-registration
	 *
	 * @param int $user_id
	 * @param int $selection_process_id
	 * @return boolean
	 */
    public static function verifyUserPermission($userId, $classroomId)
    {
		$table  = new Tri_Db_Table('selection_process');
		$select = $table->select()
                        ->where('user_id = ?', $userId)
                        ->where('classroom_id = ?', $classroomId);
		$data = $table->fetchRow($select);
        
		if ($data) {
			return false;
		}
		return true;
    }

	/**
	 * Get all the students interested in courses of the selection process
	 *
	 * @param int $id
	 * @param int $course_id
	 * @return object select
	 */
    public static function listPreRegistration($course_id = null)
    {
		$selectionProcessUser = new Tri_Db_Table('selection_process');
		$select = $selectionProcessUser->select()->setIntegrityCheck(false)
									   ->from(array('pu' => 'selection_process'), array('id', 'date', 'justify', 'status'))
									   ->join(array('c' => 'classroom'), 'pu.classroom_id = c.id', array('cid' => 'id', 'cname' => 'name'))
									   ->join(array('co' => 'course'), 'c.course_id = co.id', array('coid' => 'id', 'coname' => 'name'))
									   ->join(array('u' => 'user'), 'u.id = pu.user_id', array('uid' => 'id', 'uname' => 'name', 'image'))
									   ->order(array('pu.status','pu.id DESC', 'u.name'));
        if ($course_id) {
			$select->where('c.course_id = ?', $course_id);
		}
        
        return $select;
    }
	
	/**
	 * Get all courses it's available to selection process
	 *
	 * @param int $selection_process_id
	 * @return array
	 */
	public static function getCourses()
	{
		$table  = new Tri_Db_Table('selection_process');
		$select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->from(array('p' => 'selection_process'), array())
                        ->join(array('c' => 'classroom'), "p.classroom_id = c.id", array())
                        ->join(array('co' => 'course'), 'c.course_id = co.id', array('id', 'name'));
        return $table->fetchAll($select);
	}
}
