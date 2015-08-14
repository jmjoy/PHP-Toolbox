<?php

class SQLBuilder {

}

interface IWhere {
    function toSql();
}

class Where implements IWhere {

    const CONDITION_EQ = '=';
    const CONDITION_NEQ = '!=';
    const CONDITION_GT = '>';
    const CONDITION_LT = '<';

    public function toSql() {

    }

}

class WhereRelation implements IWhere {
    
    const RELATION_AND = 'AND';
    const RELATION_OR  = 'OR';

    public function Add(IWhere $where) {

    }

    public function Remove(IWhere $where) {

    }

    public function toSql() {

    }

}
