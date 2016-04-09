<?php


class mongoOps {
    
    public $CONN;
    public $DB;
    public $MONGO_COLL_MECHS;
    public $MONGO_COLL_PDFS;
    
    public function __construct()
    {
        require_once('../config/config.php');
        $this->CONN = new Mongo('mongodb://127.0.0.1:27017');
        $this->DB = $this->CONN->selectDB(MONGO_DB);
        $this->MONGO_COLL_MECHS = MONGO_COLL_MECHS;
        $this->MONGO_COLL_PDFS = MONGO_COLL_PDFS;
        
        $arguments = func_get_args();

        if(!empty($arguments)) {
            foreach($arguments[0] as $key => $property) {
                if(property_exists($this, $key)) {
                    $this->{$key} = $property;
                }
            }
        }
    }
    
    public function find($command, $returnKeys, $COLL) {
        
        $collToPoll = $this->DB->selectCollection($this->$COLL);
        
        $cursor = $collToPoll->find($this->decodeCommand($command), $this->decodeCommand($returnKeys));
        
        return $cursor;
    }
    
    public function insert($command, $COLL) {
        
        $collToInsert = $this->DB->selectCollection($this->$COLL);
        $collToInsert->insert($command);
        
        return $command;
    }
    
    public function objFromCursor($cursor) {
        
        $finalReturns = new stdClass();
        
        foreach($cursor as $doc) {
            $finalReturns->{$doc['_id']} = $doc;
            unset($finalReturns->{$doc['_id']}['_id']);
        }
        
        return $finalReturns;
    }
    
    public function decodeCommand($command) {
        $comm = json_decode($command, true);
        
        foreach($comm as $key => $val) {
            
            $validCommands = ['$gte', '$gt', '$lt', '$lte', '_id'];
            
            if (in_array($key, $validCommands) && (strlen($val) == 24)) {
                
                $thisVar = new MongoId($val);
                $comm[$key] = $thisVar;
            }
            else if (is_array($val)) {
                $comm[$key] = $this->decodeCommand(json_encode($val));
            }
        }
        
        return $comm;
    }
}


?>
