<?php
namespace Qbus\Qbtools\ViewHelpers\Widget;

/**
 * Mailform widget with configurable form fields
 *
 * Example:
 *
 * {namespace q=Qbus\Qbtools\ViewHelpers}
 * <q:widget.mailform recipient="{mail: 'receiver@example.com', name: 'Receiver Name'}"
 *   required="{0: 'firstname', 1: 'lastname', 2: 'email'}"
 *   mailTemplate="EXT:your_site/Resources/Private/Templates/Mailform/Mail.txt">
 *   <div class="qbtools-mailform-notification">
 *     <div class="alert alert-success" data-status="success" style="display: none">You message has not been sent.</div>
 *     <div class="alert alert-danger" data-status="fields-missing" style="display: none">Fields marked with * have to be filled.</div>
 *     <div class="alert alert-danger" data-status="error" style="display: none">Your E-Mail could not be sent. An internal error happended.</div>
 *   </div>
 *   <f:form.textfield name="firstname" required="true" id="{qbmailformid}-firstname" class="form-control" placeholder="Firstname *" />
 *   <f:form.textfield name="lastname"  required="true" id="{qbmailformid}-lastname"  class="form-control" placeholder="Lastname *" />
 *   <f:form.textfield name="email"     required="true" type="email" id="{qbmailformid}-email" class="form-control" placeholder="E-Mail *"/>
 *   <f:form.submit value="Anfrage absenden"/>
 *  </q:widget.mailform>
 *
 *  NOTE: You may set config.tx_qbtools.mailform.receiver.overwrite.email = your@dev.email
 *
 * TODO: accept required as string-list
 * TODO: accept required list with conditions?
 * TODO: accept recipient as simple string containing only the mail address
 */
class MailformViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper
{
    /**
     * @var bool
     */
    protected $ajaxWidget = true;

    /**
     * @bar bool
     */
    protected $storeConfigurationInSession = false;

    /**
     * @var \Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController
     */
    protected $controller;

    /**
     * @param  \Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController $controller
     * @return void
     */
    public function injectController(\Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param  array  $recipient    = array('email': "mail@address", 'name' => "Full Name");
     * @param  array  $sender       = array('email': "mail@address", 'name' => "Full Name");
     * @param  array  $required     = array("firstname", "lastname", "email");
     * @param  string $mailTemplate
     * @return string
     */
    public function render($recipient, $sender = null, $required = array('firstname', 'lastname', 'email', 'message'),
            $mailTemplate = 'EXT:qbtools/Resources/Private/Templates/Mailform/Mail.txt')
    {

        /* <f:renderChildren> does not include the variable context from the  subrequest-controller,
         * therefore we set the desired variables here. */
        $this->viewHelperVariableContainer->add('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'fieldNamePrefix', 'msg');
        $result = $this->initiateSubRequest();
        $this->viewHelperVariableContainer->remove('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'fieldNamePrefix');

        return $result;
    }
}
