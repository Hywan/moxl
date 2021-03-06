<?php
/*
 * Delete.php
 * 
 * Copyright 2012 edhelas <edhelas@edhelas-laptop>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

namespace Moxl\Xec\Action\Pubsub;

use Moxl\Xec\Action;
use Moxl\Xec\Action\Pubsub\Errors;
use Moxl\Stanza\Pubsub;

class Delete extends Errors
{
    private $_to;
    private $_node;
    
    public function request() 
    {
        $this->store();
        Pubsub::delete($this->_to, $this->_node);
    }
    
    public function setTo($to)
    {
        $this->_to = $to;
        return $this;
    }
    
    public function setNode($node)
    {
        $this->_node = $node;
        return $this;
    }
    
    public function handle($stanza, $parent = false) {
        if($stanza["type"] == "result"){
            //delete from bookmark
            $sd = new \Modl\SubscriptionDAO();
            $sd->deleteNode($this->_to, $this->_node);
            
            //delete from item
            $id = new \Modl\ItemDAO();
            $id->deleteItem($this->_to, $this->_node);

            $this->pack(array('server' => $this->_to, 'node' => $this->_node));
            $this->deliver();
        }
    }

    public function error($stanza, $parent = false) {
        //delete from bookmark
        $sd = new \Modl\SubscriptionDAO();
        $sd->deleteNode($this->_to, $this->_node);
        
        //delete from item
        $id = new \Modl\ItemDAO();
        $id->deleteItem($this->_to, $this->_node);

        $this->pack(array('server' => $this->_to, 'node' => $this->_node));
        $this->deliver();
    }
}
