<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Remesh Babu S  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Tester\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Listener\Listener;


class TesterController extends AbstractActionController
{
  protected $testerTable;

  public function indexAction()
  {
    $btn_1 = $this->CommonPlugin()->checkACL($_SESSION['authUserID'], "btn_1");
    $btn_2 = $this->CommonPlugin()->checkACL($_SESSION['authUserID'], "btn_2");
    return new ViewModel(array(
        'acl_bitton_1' => $btn_1,
        'acl_bitton_2' => $btn_2,
    ));
  }
  
  public function getTesterTable()
  {	
    if (!$this->testerTable) {
      $sm = $this->getServiceLocator();
      $this->testerTable = $sm->get('Tester\Model\TesterTable');
    }
    return $this->testerTable;
  }
}