<?php

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class mongoOps
{

    public $CONN;
    public $DB;
    public $MONGO_COLL_MECHS;
    public $MONGO_COLL_PDFS;
    public $MONGO_COLL_MARKUPS;

    public function __construct()
    {
        require_once '../config/config.php';

        // Updated to use MongoDB\Client for the new MongoDB driver
        $this->CONN = new MongoDB\Client(MONGO_URI);
        $this->DB = $this->CONN->selectDatabase(MONGO_DB); // selectDatabase method

        // Collection names
        $this->MONGO_COLL_MECHS = MONGO_COLL_MECHS;
        $this->MONGO_COLL_PDFS = MONGO_COLL_PDFS;
        $this->MONGO_COLL_MARKUPS = MONGO_COLL_MARKUPS;

        // Handling arguments to update properties
        $arguments = func_get_args();
        if (!empty($arguments)) {
            foreach ($arguments[0] as $key => $property) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $property;
                }
            }
        }
    }

    // Updated the method to use new MongoDB driver syntax for find
    public function find($command, $returnKeys, $COLL)
    {

        $collToPoll = $this->DB->selectCollection($this->$COLL);

        $options = ['projection' => $this->decodeCommand($returnKeys)];
        $cursor = $collToPoll->find($this->decodeCommand($command), $options);

        return $cursor;
    }

    // Updated the method to use new MongoDB driver syntax for update
    public function update($command, $newData, $COLL)
    {

        $collToPoll = $this->DB->selectCollection($this->$COLL);

        // Use updateOne or updateMany (depending on requirement)
        $result = $collToPoll->updateOne($this->decodeCommand($command), $newData);

        return $result;
    }

    // Updated insert method to use insertOne for inserting a single document
    public function insert($command, $COLL)
    {

        $collToInsert = $this->DB->selectCollection($this->$COLL);
        $result = $collToInsert->insertOne($command);  // Use insertOne for a single document

        return $result;
    }

    // Updated remove method to use deleteOne or deleteMany
    public function remove($command, $COLL)
    {

        $collToRemove = $this->DB->selectCollection($this->$COLL);
        $result = $collToRemove->deleteOne($command); // Use deleteOne or deleteMany

        return $result;
    }

    // Updated method to process the cursor data correctly
    public function objFromCursor($cursor)
    {

        $finalReturns = new stdClass();

        foreach ($cursor as $doc) {
            $finalReturns->{$doc['_id']} = $doc;
            unset($finalReturns->{$doc['_id']}['_id']);
        }

        return $finalReturns;
    }

    // Decode command for handling MongoDB types (e.g., ObjectId)
    public function decodeCommand($command)
    {

        if (is_object($command))
            $command = (array) $command;
        if (!is_scalar($command))
            $command = json_encode($command);
        $comm = json_decode($command, true);

        foreach ($comm as $key => $val) {

            $validCommands = ['$gte', '$gt', '$lt', '$lte', '_id'];

            // Handle MongoDB ObjectId conversion
            if (in_array($key, $validCommands) && is_string($val) && (strlen($val) == 24)) {
                $comm[$key] = new MongoDB\BSON\ObjectId($val);  // Updated ObjectId constructor
            } else if (is_array($val)) {
                $comm[$key] = $this->decodeCommand(json_encode($val));
            }
        }

        return $comm;
    }
}

?>