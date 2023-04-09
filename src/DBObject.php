<?php

namespace kirillreutski\DbGenericObject; 

class DBObject {
    public int $id; 
    public static string $table; 
    protected static array $fields = ['id']; 

    public function __construct(array $data){
        foreach (static::$fields as $field) {
            if (isset($data[$field])) {
                $this->{$field} = $data[$field];
            }
        }
        return $this; 
    }
    public static function init(array $data) : static {
        $obj = new static($data); 
        return $obj; 
    }

    public function save(){ 

        $pairs = []; 
        $fieldValues = [];
        foreach (static::$fields as $field) {
            if ($field !== 'id') {
                if (isset($this->{$field})) {
                    $value = $this->{$field};
                    if (gettype($value) == 'array') {
                        $pairs[] = "$field = '" . json_encode($value) . "'";
                    } else {
                        $pairs[] = "$field = '" . $value . "'";
                    }
                    
                    $fieldValues[$field] = $value;
                }
            }
                
        }
        $query = "UPDATE " . static::$table . " SET " . implode(', ', $pairs) . " WHERE id = " . $this->id . " LIMIT 1"; 
        $queryData = [
            'type' => 'UPDATE', 
            'query' => $query, 
            'table' => static::$table, 
            'fieldValues' => $fieldValues, 
            'id' => $this->id
        ];
        $result = static::runQuery($queryData);
    }

    public function dump() : array {
        $out = []; 
        foreach (static::$fields as $field) {
            if (isset($this->{$field})) {
                $out[$field] = $this->{$field};
            }

        }

        return $out; 
    }

    public static function initById(int $id){
        $query = "SELECT " . implode(', ', static::$fields) . " FROM " . static::$table . " WHERE id = $id LIMIT 1"; 
    
        $queryData = [
            'type' => 'SELECT', 
            'query' => $query, 
            'table' => static::$table, 
            'fields' => static::$fields, 
            'id' => $id
        ];

        $data = static::runQuery($queryData);
        return new static($data[0]);
    }

    public static function initByX(string $fieldName, mixed $value){
        $adjustedValue = gettype($value) == 'string' ? "'$value'" : $value;  
        $query = "SELECT " . implode(', ', static::$fields) . " FROM " . static::$table . " WHERE $fieldName = $adjustedValue LIMIT 1"; 
    
        $queryData = [
            'type' => 'SELECT', 
            'query' => $query, 
            'table' => static::$table, 
            'fields' => static::$fields, 
            'id' => $id
        ];

        $data = static::runQuery($queryData);
        return new static($data[0]);
    }

    

    

    public static function runQuery(array $queryData){

    }
}