<?php

$where1 = new Where(Where::CONDITION_EQ);
$where1->setName('id');
$where1->setValue(5);
$where1->setType(Where::TYPE_INT);

$where2 = new Where(Where::CONDITION_NEQ, 'name', 'jmjoy', Where::TYPE_STRING);

$where3 = new WhereRelation(WhereRelation::RELATION_AND);
$where3->add($where1);
$where3->add($where2);

$where4 = new WhereRelation(WhereRelation::RELATION_OR, array($where3));

$where5 = new WhereRelation(WhereRelation::RELATION_OR, array());

$query = SQLBuilder::query();
$query->select(array('*'))
    ->where(new WhereRelation(WhereRelation::RELATION_AND, array(
        new Where(Where::CONDITION_EQ, 'name', 'jmjoy', Where::TYPE_STRING),
        new Where(Where::CONDITION_EQ, 'name', 'jmjoy', Where::TYPE_STRING)
    )))
    ->order(array('id desc'))
    ->offset(10)
    ->limit(5)
    ->toSql();
