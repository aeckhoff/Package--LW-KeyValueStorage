<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 2013 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

namespace LwKeyValueStorage\Model\KeyValueStorage\CommandResolver;

class getEntriesByEntityClass extends \LWmvc\Model\CommandResolver
{
    protected $command;
    
    public function __construct($command)
    {
        parent::__construct($command);
        $this->setBaseNamespace('\LwKeyValueStorage\Model\KeyValueStorage\\');
    }
    
    public function getInstance($command)
    {
        return new getEntriesByEntityClass($command);
    }
    
    public function resolve()
    {
        $queryHandler = $this->getQueryHandler();
        $queryHandler->setEntityClass($this->command->getParameterByKey('entityclass'));
        $queryHandler->setAttributes($this->command->getParameterByKey('attributes'));
        $result = $queryHandler->loadEntitesByEntityClass($this->command->getParameterByKey('filter'));
        $this->command->getResponse()->setParameterByKey('EntryCollection', $result);
        return  $this->command->getResponse();
    }
}
