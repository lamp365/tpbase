<?php

class Auth extends \Think\Auth
{
	public function getAccessList($uid, $type){
		
		return $this->getAuthList($uid, $type);
		
	}
}