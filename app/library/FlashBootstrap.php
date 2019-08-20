<?php
class FlashBootstrap extends \Phalcon\Flash\Direct
{
    public function message($type, $message)
    {
        $cssClass = $this->_cssClasses[$type];

        $message = <<<STR
<div class="{$cssClass} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
    {$this->getEscaperService()->escapeHtml($message)}
</div>
STR;

        parent::message($type, $message);
    }
}