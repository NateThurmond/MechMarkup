<?php
/*
    Author: Nathan Thurmond
    Name: sqlPrepare.php
    Date: 9/15/2015
    Last Updated: 2/1/2016
    Usage: This script contains custom functions used to execute sql prepared statements.
        This provides added security and methods for getting data in a consistent format.
*/

/*
    bindFetch prepares a sql statement and executes it. It returns data
    and is self closing.
*/
function bindFetch($stmt, $valArray) {
    
    // Prepare the statement
    prepareStatment($stmt, $valArray);
    // Execute it
    mysqli_stmt_execute($stmt);
    
    $array = array();
    
    if($stmt instanceof mysqli_stmt)
    {
        $stmt->store_result();
        
        $variables = array();
        $data = array();
        $meta = $stmt->result_metadata();
        
        while($field = $meta->fetch_field())
            $variables[] = &$data[$field->name]; // pass by reference
        
        call_user_func_array(array($stmt, 'bind_result'), $variables);
        
        $i=0;
        while($stmt->fetch()) {
            $array[$i] = array();
            foreach($data as $k=>$v)
                $array[$i][$k] = $v;
            $i++;
        }
    }
    elseif($stmt instanceof mysqli_result)
    {
        while($row = $stmt->fetch_assoc())
            $array[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $array;
}

/*
    Bind execute prepares a sql statement and executes it. It
    returns true or false based on the failure or success of the query.
    Not self-closing.
*/
function bindExecute($stmt, $valArray) {  
    
    prepareStatment($stmt, $valArray);
    
    return mysqli_stmt_execute($stmt);
}

/*
    PrepareStatement looks at values passed to it, determines
    their variable type, integer, string, etc. and then binds it
    to the sql prepared statement.
*/
function prepareStatment($stmt, $valArray) {
    
    // String containing list of bind types
    $bindTypes = '';
    $valArrayRefs = array();
    
    foreach ($valArray as $key => $val) {
        // Append to the bind types based on the first character of the variable type passed
        $bindTypes .= gettype($val)[0];
        
        if (strnatcmp(phpversion(),'5.3') >= 0) { //Reference is required for PHP 5.3+
            $valArrayRefs[$key] = &$valArray[$key];
        }
        else {
            $valArrayRefs = $valArray;
        }
    }
    
    // Bind the types to the statement
    call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt, $bindTypes), $valArrayRefs)); 
}