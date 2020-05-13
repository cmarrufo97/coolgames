<?php

namespace app\services;

class Comentario
{
    public $id;
    public $children;
    public $content;
    public $isRoot;

    public function __construct($id, $content)
    {
        $this->id = $id;
        $this->content = $content;
        $this->children = array();
        $this->isRoot = true;
    }
    public function addChild($child)
    {
        $child->isRoot = false;
        $this->children[] = $child;
    }
    public function getChildren()
    {
        return $this->children;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getContent()
    {
        return $this->content;
    }
}
