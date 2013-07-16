Package--LW-KeyValueStorage
===========================

The LW Key-Value-Storage is a simple Package for saving Key-Value type data.
It depends on the Contentory System V3.8+.

The package provides in this first version 5 function, that can be called via the LwMVC CommandDispatch:

- addEntry
- saveEntryById
- deleteEntryById
- getEntryById
- getEntriesByEntityClass


This is a very short introduction showing just the basics. A more detailed explanation will follow soon.

All but the deleteEntryB4yId functions need an attribute array, that provides the data structure. The specific key stands for filter columns in the entity table. 

Example:

    $attributes = array(
        array("key"=>"id", "type"=>"number"),
        array("key"=>"name", "type"=>"text", "size" => 255, "specific"=>"opt01text", "required" => 1),
        array("key"=>"email", "type"=>"text", "size" => 255),
        array("key"=>"date", "type"=>"number", "size" => 14),
        array("key"=>"flag", "type"=>"number", "size" => 1, "specific"=>"opt01bool"),
        array("key"=>"amount", "type"=>"number", "size" => 5, "specific"=>"opt01number")
    );

To address a specific data structure, the systems needs an entity class name, that must be provided via a parameter for all functions described. This entity class can be seen as a table name and the attributes as the columns in that table.

    'entityclass'=> 'test_test'

    
To add or save data, a second array with the data is needed:

    $data = array(
        "name"=>"tester".date("s"), 
        "email" => "tester".date("s")."@test.de", 
        "date"=>date("YmdHis")
    );
    
    
To add the sample data to the storage, defined by the attributes array above, you can use this code:

    $Response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwKeyValueStorage', 'KeyValueStorage', 'addEntry', array('entityclass'=> 'test_test', "attributes"=>$attributes), array("data"=>$data));

The Id of the new entry can be retreived from the Response Object:

    $newId = $Response->getParameterByKey("id");

To save the sample data to an entry with id=2 in the storage, defined by the attributes array above, you can use this code:

    $Response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwKeyValueStorage', 'KeyValueStorage', 'saveEntryById', array('entityclass'=> 'test_test', 'id' => 2, "attributes"=>$attributes), array("data"=>$data));

To delete an entry with the id=3, you can use this code:

    $Response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwKeyValueStorage', 'KeyValueStorage', 'deleteEntryById', array('entityclass'=> 'test_test', 'id' => 3));

To load the entry with id=2, you can use this code:

    $Response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwKeyValueStorage', 'KeyValueStorage', 'getEntryById', array('entityclass'=> 'test_test', 'id' => 2, "attributes"=>$attributes));

You can retreive the entry object (see lw_mvc plugin) from the Response Object:

    $entry = $Response->getDataByKey("Entry");

To load all Entries for a specific Entity Class, you can use this code:

    $Response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwKeyValueStorage', 'KeyValueStorage', 'getEntriesByEntityClass', array('entityclass'=> 'test_test', "attributes"=>$attributes));

You can retreive the entries collection (see lw_mvc plugin) from the Response Object:

    $entryCollection = $Response->getDataByKey("EntryCollection");
    
To load filtered Entries for a specific Entity Class, you can use this code:

    $filterArray = array(
        array("", "amount", ">", "300"),
        array("AND", "name", "like", "tester%")
    );
    $Response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwKeyValueStorage', 'KeyValueStorage', 'getEntriesByEntityClass', array('entityclass'=> 'test_test', "attributes"=>$attributes, "filter" => $filterArray));
    

