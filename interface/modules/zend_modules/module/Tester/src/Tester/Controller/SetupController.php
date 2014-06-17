<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*    @author  Remesh Babu S <remesh@zhservices.com>
* +------------------------------------------------------------------------------+
*/

namespace Tester\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Listener\Listener;
use Tester\Form\SetupForm;

class SetupController extends AbstractActionController
{
   protected $listenerObject;
   public function __construct()
   {
    $this->listenerObject	= new Listener;
   }
  /**
   * Index Page
   * 
   * @return \Zend\View\Model\ViewModel
   */
  public function indexAction()
  {
    $form 	= new SetupForm();
    $this->layout('tester/layout/setup');

    return new ViewModel(array(
                          'form'            => $form, 
                          'listenerObject'  => $this->listenerObject,
                      ));
  }
  
  /**
   * Function savedataAction
   * Save all Data 
   * 
   * @return type
   */
  public function savedataAction()
  {
     $request    = $this->getRequest();
     $this->layout('tester/layout/setup');
     return $this->redirect()->toRoute('tester-setup');
  }
  
  /**
   * Funtion getTitle
   * Setup Title settings at Configuration View
   * 
   * @return string
   */
  public function getTitle()
  {
    $title = "Tester Setup";
    return $title;
  }
}

