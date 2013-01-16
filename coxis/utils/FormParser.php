<?php
namespace Coxis\Utils;

class Field {
    protected $value;
    protected $choices = array();
    protected $type;

    function __construct($node) {
        $nodeName = $node->nodeName;

        switch($nodeName) {
            case 'input':
                $inputType = $node->getAttribute('type');
                $inputValue = $node->getAttribute('value');
                $this->type = $inputType;
                switch($inputType) {
                    case 'text':
                    case 'password':
                    case 'hidden':
                        $this->value = $inputValue;
                        break;
                    case 'submit':
                    case 'image':
                        // ...
                        $this->value = $inputValue;
                        break;
                    case 'radio':
                    case 'checkbox':
                        $this->addChoice($node);
                        break;
                    case 'file':
                        break;
                }
                break;
            case 'select':
                $this->type = $nodeName;
                $multiple = $node->getAttribute('multiple') == 'multiple';

                $document = new \DOMDocument('1.0', 'UTF-8');
                $node = $document->importNode($node, true);
                $root = $document->appendChild($document->createElement('_root'));
                $root->appendChild($node);
                $xpath = new \DOMXPath($document);
                
                foreach($xpath->query('descendant::option', $root) as $option_node) {
                    $value = $option_node->getAttribute('value');
                    $this->choices[] = $value;
                    if($option_node->getAttribute('selected') == 'selected') {
                        if($multiple)
                            $this->value[] = $value;
                        else
                            $this->value = $value;
                    }
                }
                break;
            case 'textarea':
                $this->type = $nodeName;
                $nodeValue = $node->nodeValue;
                $this->value = $nodeValue;
                break;
        }
    }

    public function addChoice($node) {
        if($node->nodeName != 'input')
            return;
        $inputValue = $node->getAttribute('value');
        $this->choices[] = $inputValue;
        switch($node->getAttribute('type')) {
            case 'radio':
                if($node->getAttribute('checked') == 'checked')
                    $this->value = $inputValue;
                break;
            case 'checkbox':
                if(!is_array($this->value))
                    $this->value = array();
                if($node->getAttribute('checked') == 'checked')
                    // $this->value[] = $inputValue;
                    $this->value = $inputValue;
                break;
        }
    }

    public function getValue() {
        return $this->value;
    }

    public function getType() {
        return $this->type;
    }
}

class FormParser {
    protected $fields = array();
    protected $submit = null;

    public function has($name) {
        return isset($this->fields[$name]);
    }

    public function get($name) {
        return $this->fields[$name];
    }

    public function set($name, $value) {
       $this->fields[$name] = $value;
    }

    public function add($node) {
        $name = $node->getAttribute('name');
        if($this->has($name))
            $this->get($name)->addChoice($node);
        else
            $this->set($name, new Field($node));
    }

    protected function getPath($name) {
        $path = array();
        $matches = null;
        preg_match('/^([^\[]+)/', $name, $matches);
        $path[] = $matches[0];
        preg_match_all('/\[([^\]]*)\]/', $name, $matches);
        $path = array_merge($path, $matches[1]);

        return $path;
    }

    public function values() {
        $res = array();
        foreach($this->fields as $name=>$field) {
            $value = $field->getValue();
            if($value === null)
                continue;
            if(($field->getType() == 'image' || $field->getType() == 'submit') && $name !== $this->submit)
                continue;

            $path = $this->getPath($name);

            $arr =& $res;
            $key = array_pop($path);
            
            foreach($path as $parent)
                $arr =& $arr[$parent];
            if(!$key)
                $arr[] = $value;
            else
                $arr[$key] = $value;
        }
        return $res;
    }

    public function clickOn($submit) {
        $this->submit = $submit;
    }

    public function parse($node) {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $node = $document->importNode($node, true);
        $root = $document->appendChild($document->createElement('_root'));
        $root->appendChild($node);
        $xpath = new \DOMXPath($document);

        foreach ($xpath->query('descendant::input | descendant::textarea | descendant::select', $root) as $node) {
            if (!$node->hasAttribute('name'))
                continue;
            $this->add($node);
        }
    }
}