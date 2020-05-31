<?php

namespace app\services;

/**
 * Clase destinada para ayudar a obtener todos los hijos de un Comentario, si es que tiene.
 */
class Comentario
{
    /**
     * ID del comentario
     *
     * @var [type]
     */
    public $id;
    /**
     * Comentario hijo del comentario.
     *
     * @var [type]
     */
    public $children;
    /**
     * Contenido del comentario.
     *
     * @var [type]
     */
    public $content;
    /**
     * Si un comentario no tiene hijos es root, si tiene no es root.
     *
     * @var [type]
     */
    public $isRoot;

    /**
     * Constructor de la clase
     *
     * @param [type] $id
     * @param [type] $content
     */
    public function __construct($id, $content)
    {
        $this->id = $id;
        $this->content = $content;
        $this->children = array();
        $this->isRoot = true;
    }

    /**
     * Añade un hijo al comentario
     *
     * @param [type] $child
     * @return []
     */
    public function addChild($child)
    {
        $child->isRoot = false;
        $this->children[] = $child;
    }

    /**
     * Obtiene y devuelve los hijos de un comentario.
     *
     * @return void
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Obtiene y devuelve el id de un comentario
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtiene y devuelve el contenido de un comentario.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
