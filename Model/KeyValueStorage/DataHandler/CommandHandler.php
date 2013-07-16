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

class CommandHandler extends \LWmvc\Model\DataCommandHandler
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
    
    public function isValid($dto)
    {
        foreach($this->attributes as $attribute)
        {
            $value = trim($dto->getValueByKey($attribute['key']));
            if (strstr(":number:bool:text:longtext:", ':'.$attribute['type'].':')) {
                $ValueColumn = 'value_'.$attribute['type'];
            }
            else {
                return false;
            }
            if ($attribute['required'] == 1 && !$value) {
                return false;
            }
            if ($attribute['size'] > 0 && strlen($value) > $attribute['size']) {
                return false;
            }
        }
        return true;
    }
    
    public function add($array)
    {
        if ($this->isValid($array)) {
            $EntityId = $this->addEntity();
            if ($EntityId > 0) {
                $ok = $this->saveValues($EntityId, $array);
                exit();
                return $EntityId;
            }
            throw new AddEntityErrorException();
        }
        throw new KeyValueDataNotValidException();
    }
    
    public function addEntity() 
    {
        $sql = "INSERT INTO ".$this->EntityTable." (entityclass, lw_first_date, lw_last_date) VALUES ('".$this->entityclass."', '".date('YmdHis')."', '".date('YmdHis')."')";
        return $this->db->dbinsert($sql);
    }
    
    public function touchEntity($id)
    {
        $sql = "UPDATE ".$this->EntityTable." SET lw_last_date = '".date('YmdHis')."' WHERE id = ".$id;
        return $this->db->dbquery($sql);
    }
    
    public function saveValues($EntityId, $dto)
    {
        foreach($this->attributes as $attribute)
        {
            $value = trim($dto->getValueByKey($attribute['key']));
            if (strlen($value)>0) {
                if(strstr(":opt01number:opt01bool:opt01text:", ':'.$attribute['specific'].':')) {
                    $sql = "UPDATE ".$this->EntityTable." SET ".$attribute['specific']." = '".$this->db->quote($value)."' WHERE id = ".$EntityId;
                    $ok = $this->db->dbquery($sql);
                }
                if (strstr(":number:bool:text:longtext:", ':'.$attribute['type'].':')) {
                    $ValueColumn = 'value_'.$attribute['type'];
                    $sql = "INSERT INTO ".$this->ValueTable." (entity_id, keyname, ".$ValueColumn.") VALUES ('".$EntityId."', '".$attribute['key']."', '".$this->db->quote($value)."')";
                    $ok = $this->db->dbquery($sql);
                }
                else {
                    throw new InvalidAttributeTypeException();
                }
                //echo $sql."<br/>";
            }
        }
    }
    
    public function save($id, $dto)
    {
        if ($this->isValid($dto)) {
            $this->touchEntity($id);
            return $this->updateValues($id, $dto);
        }
        throw new KeyValueDataNotValidException();
    }
    
    public function updateValues($id, $dto)
    {
        $ok = $this->deleteValues($id);
        if ($ok) {
            return $this->saveValues($id, $dto);
        }
        throw new DeleteValuesErrorException();
    }

    public function deleteValues($id) 
    {
        $sql = "DELETE FROM ".$this->ValueTable." WHERE entity_id = ".$id;
        return $this->db->dbquery($sql);
    }

    public function deleteEntity($id) 
    {
        $sql = "DELETE FROM ".$this->EntityTable." WHERE id = ".$id;
        return $this->db->dbquery($sql);
    }
    
    public function delete($id)
    {
        $ok = $this->deleteValues($id);
        if ($ok) {
            return $this->deleteEntity($id);
        }
        throw new DeleteEntityErrorException();
    }
}
