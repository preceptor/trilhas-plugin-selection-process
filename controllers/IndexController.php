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
 * @package    SelectionProcess_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_IndexController extends Tri_Controller_Action
{
	/**
	 * Init
 	 *
	 * Call parent init and set title box.
	 *
	 * @return void
	 */
    public function init()
    {
        parent::init();
        $this->_helper->layout->setLayout('admin');
        $this->view->title = "Selection process";
    }
	
	/**
	 * Action index.
	 *
	 * @return void
	 */
    public function indexAction()
    {
		$page   = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query  = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
		$course = Zend_Filter::filterStatic($this->_getParam('course'), 'int');
		$select = SelectionProcess_Model_SelectionProcess::listPreRegistration($course);
        if ($query) {
            $select->where('u.name LIKE (?)', "%$query%");
        }
		$table = new Tri_Db_Table('selection_process');
		$this->view->courses = $this->toSelect(SelectionProcess_Model_SelectionProcess::getCourses()->toArray());
		$this->view->course_id = $course;
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

    public function viewAction()
    {
        $this->_helper->layout->setLayout('layout');
		$table = new Tri_Db_Table('selection_process');
        $select = $table->select()->setIntegrityCheck(false)
                        ->from(array('pu' => 'selection_process'), array('id', 'date', 'justify', 'status'))
                        ->join(array('c' => 'classroom'), 'pu.classroom_id = c.id', array('cid' => 'id', 'cname' => 'name'))
                        ->join(array('co' => 'course'), 'c.course_id = co.id', array('coid' => 'id', 'coname' => 'name'))
                        ->where('pu.user_id = ?', Zend_Auth::getInstance()->getIdentity()->id)
                        ->where('pu.status <> ?', SelectionProcess_Model_SelectionProcess::ACCEPTS);

        $this->view->data = $table->fetchAll($select);
    }

	public function signAction()
	{
        $this->_helper->layout->setLayout('solo');
		$form    = new SelectionProcess_Form_PreRegister();
        $table   = new Tri_Db_Table('selection_process');
        $data    = $this->_getAllParams();
		$user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $data['classroom_id'] = $id;
        }
        
        $result = SelectionProcess_Model_SelectionProcess::verifyUserPermission($user_id, $data['classroom_id']);

        if (false === $result) {
            $this->_helper->_flashMessenger->addMessage('Error pre-register');
            $this->_redirect('index');
        }
        
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($data)) {
	            $data = $form->getValues();
                $data['user_id'] = $user_id;
                $data['status']  = SelectionProcess_Model_SelectionProcess::WAITING;
                $row  = $table->createRow($data);
                
				if ($row->save()) {
				    $this->_helper->_flashMessenger->addMessage('Success');
		            $this->_redirect('dashboard');
				}
	        }
		}

		$form->populate($data);
        $this->view->form = $form;
	}
	
	/**
	 * Action matriculate
	 *
	 * @return void
	 */
	public function matriculateAction()
	{
		$post = $this->_getAllParams();
        
		if (count($post['interested'])) {
			$table = new Tri_Db_Table('selection_process');
            $tableClassRoom = new Tri_Db_Table('classroom_user');
            
			foreach ($post['interested'] as $interested) {
				$i = explode('-', $interested);
                
				//Alter status of the interested to ACCEPTS
				$where['classroom_id = ?'] = $i[0];
				$where['user_id = ?'] = $i[1];
                
				$row = $table->fetchRow($where);
				$row->status = SelectionProcess_Model_SelectionProcess::ACCEPTS;
                $id = $row->save();
                
				if ($id) {
                    try{
                        //Save new student in classroom_user
                        $classroom['classroom_id'] = $i[0];
                        $classroom['user_id']      = $i[1];
                        $classroom['status']       = Application_Model_Classroom::REGISTERED;

                        $rowClass = $tableClassRoom->createRow($classroom);
                        $result   = $rowClass->save();
                    } catch(Exception $e) {}

                    $this->_helper->_flashMessenger->addMessage('Success');
                    $this->_redirect('selection-process');
				}
                
			}
		} 
		$this->_helper->_flashMessenger->addMessage('Error');
        $this->_redirect('selection-process');
	}
	
	/**
	 * Action matriculate
	 *
	 * @return void
	 */
	public function rejectAction()
	{
		$post = $this->_getAllParams();
        
		if (count($post['interested'])) {
			$table = new Tri_Db_Table('selection_process');
			foreach ($post['interested'] as $interested) {
				$i = explode('-', $interested);
				//Alter status of the interested to ACCEPTS
				$where['classroom_id = ?'] = $i[0];
				$where['user_id = ?'] = $i[1];
				$row = $table->fetchRow($where);
				$row->status = SelectionProcess_Model_SelectionProcess::REJECTED;
                $id = $row->save();
                
				if ($id) {
					$this->_helper->_flashMessenger->addMessage('Success');
			        $this->_redirect('selection-process');
				}
			}
		} 
		$this->_helper->_flashMessenger->addMessage('Error');
        $this->_redirect('selection-process');
	}
	
	/**
	 * Arranges data to select tag
	 *
	 * @param array $datas	
	 * @return array
	 */
	public function toSelect($datas)
	{
		$result = array('' => $this->view->translate('[select]'));
		foreach ($datas as $data) {
			$result[$data['id']] = $data['name'];
		}
		return $result;
	}
}