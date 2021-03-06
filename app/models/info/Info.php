<?php

namespace Modl;

use Movim\Picture;

class Info extends Model
{
    public $server;
    public $node;
    public $category;
    public $type;
    public $name;
    public $description;
    public $occupants;
    public $created;
    public $updated;

    public $num;
    public $logo = false;
    public $subscription;

    public $_struct = [
        'server'    => ['type' => 'string','size' => 64,'key' => true],
        'node'      => ['type' => 'string','size' => 96,'key' => true],

        'category'  => ['type' => 'string','size' => 16],
        'type'      => ['type' => 'string','size' => 16],
        'name'      => ['type' => 'string','size' => 128],

        'description' => ['type' => 'text'],

        'occupants' => ['type' => 'int'],

        'created'   => ['type' => 'date'],
        'updated'   => ['type' => 'date','mandatory' => true],
    ];

    public function set($query)
    {
        $this->server = (string)$query->attributes()->from;

        if(isset($query->query)) {
            $this->node     = (string)$query->query->attributes()->node;

            foreach($query->query->identity as $i) {
                if($i->attributes()) {
                    $this->category = (string)$i->attributes()->category;
                    $this->type     = (string)$i->attributes()->type;

                    if($i->attributes()->name) {
                        $this->name = (string)$i->attributes()->name;
                    } else {
                        $this->name = $this->node;
                    }
                }
            }

            if(isset($query->query->x)) {
                foreach($query->query->x->field as $field) {
                    $key = (string)$field->attributes()->var;
                    switch ($key) {
                        case 'pubsub#title':
                            $this->name = (string)$field->value;
                            break;
                        case 'pubsub#creation_date':
                            $this->created = date(SQL::SQL_DATE, strtotime((string)$field->value));
                            break;
                        case 'muc#roominfo_description':
                        case 'pubsub#description':
                            $this->description = (string)$field->value;
                            break;
                        case 'pubsub#num_subscribers':
                        case 'muc#roominfo_occupants':
                            $this->occupants = (int)$field->value;
                            break;
                    }
                }
            }
        }

        $this->updated  = date(SQL::SQL_DATE);
    }

    public function setItem($item)
    {
        $this->server = (string)$item->attributes()->jid;
        $this->node   = (string)$item->attributes()->node;
        $this->name   = (string)$item->attributes()->name;
        $this->updated  = date(SQL::SQL_DATE);
    }
}

class Server extends Info
{
    public $server;
    public $number;
    public $name;
    public $published;
}
