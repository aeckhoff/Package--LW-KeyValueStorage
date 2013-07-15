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

namespace LwKeyValueStorage\Model\KeyValueStorage\DataHandler;

class QueryHandler extends \LWmvc\Model\DataQueryHandler
{
    protected $table;
    
    public function __construct(\lw_db $db)
    {
        parent::__construct($db);
        $this->ValueTable = $this->db->gt("lw_kv_values");
        $this->EntityTable = $this->db->gt("lw_kv_entity");
    }
    
    public function setEntityClass($entityclass)
    {
        $this->entityclass = $entityclass;
    }
    
    public function setAttributes($array)
    {
        $this->attributes = $array;
    }
    
    public function getAttributeColumnByKey($key)
    {
        foreach($this->attributes as $attribute)
        {
            if ($attribute['key'] == $key) {
                if (strstr(":number:bool:text:longtext:", ':'.$attribute['type'].':')) {
                    return 'value_'.$attribute['type'];
                }
            }
        }
        return false;
    }
    
    public function loadEntityValuesById($id)
    {
        $sql = "SELECT * FROM ".$this->ValueTable." WHERE entity_id = ".$id;
        $result = $this->db->select($sql);
        
        $array['id'] = $id;
        foreach($result as $value) {
            $array[$value['keyname']] = trim($value[$this->getAttributeColumnByKey($value['keyname'])]);
        }
        $dto = new \LWmvc\Model\DTO($array);
        return \LWmvc\Model\EntityFactory::buildEntityFromDTO($this->entityclass, $dto, $id);
    }
    
    public function loadEntitesByEntityClass()
    {
        $sql = "SELECT v.* FROM ".$this->ValueTable." v, ".$this->EntityTable." e WHERE e.entityclass = '".$this->entityclass."' AND v.entity_id = e.id ORDER BY entity_id ASC";
        $result = $this->db->select($sql);
        foreach($result as $value) {
            if ($array[$value['entity_id']]['id'] < 1) {
                $array[$value['entity_id']]['id'] = $value['entity_id'];
            }
            $array[$value['entity_id']][$value['keyname']] = trim($value[$this->getAttributeColumnByKey($value['keyname'])]);
        }
        foreach($array as $entry) {
            $dtos[] = new \LWmvc\Model\DTO($entry);
        }
        return \LWmvc\Model\EntityCollectionFactory::buildCollectionFromQueryResult($this->entityclass, $dtos);
    }
}
