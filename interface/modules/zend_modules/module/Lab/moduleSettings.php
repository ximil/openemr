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
// Author:   Jacob T.Paul <jacob@zhservices.com> 
//
// +------------------------------------------------------------------------------+
/**
 *$MODULESETTINGS = array(
    'ACL'   => array(),
    'preferences' => array(),
    'hooks' => array()
    )
 */
$MODULESETTINGS = array(
    'ACL'   => array(
      "procedure_order"   =>  array("menu_name"=>"Procedure Order"),
      "pending_review"    =>  array("menu_name"=>"Pending/Review")
    ),
    'HOOKS' => array(
      "procedure_order"   => array("menu_name"=>"Procedure Order","path"=>"puiblic/lab/index")
    )
    );

?>