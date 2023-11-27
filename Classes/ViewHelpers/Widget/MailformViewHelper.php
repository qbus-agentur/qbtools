<?php

namespace Qbus\Qbtools\ViewHelpers\Widget;

use Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper;

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
class MailformViewHelper extends AbstractWidgetViewHelper
{
    /**
     * @var bool
     */
    protected $ajaxWidget = true;

    /**
     * @var bool
     */
    protected $storeConfigurationInSession = false;

    /**
     * @var MailformController
     */
    protected $controller;

    /**
     * @param  MailformController $controller
     */
    public function injectController(MailformController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('recipient', 'array', "['email' => 'mail@address', 'name' => 'Full Name']", true);
        $this->registerArgument('sender', 'array', "['email' => 'mail@address', 'name' => 'Full Name']", false, null);
        $this->registerArgument('required', 'array', "['firstname', 'lastname', 'email']", false, ['firstname', 'lastname', 'email', 'message']);
        $this->registerArgument('mailTemplate', 'string', '', false, 'EXT:qbtools/Resources/Private/Templates/Mailform/Mail.txt');
        $this->registerArgument('cc', 'array', "['email' => 'mail@address', 'name' => 'Full Name']", false, null);
        $this->registerArgument('bcc', 'array', "['email' => 'mail@address', 'name' => 'Full Name']", false, null);
    }

    /**
     * @return ResponseInterface
     */
    public function render(): ResponseInterface
    {
        /* <f:renderChildren> does not include the variable context from the  subrequest-controller,
         * therefore we set the desired variables here. */
        $this->viewHelperVariableContainer->add(FormViewHelper::class, 'fieldNamePrefix', 'msg');
        $result = $this->initiateSubRequest();
        $this->viewHelperVariableContainer->remove(FormViewHelper::class, 'fieldNamePrefix');

        return $result;
    }
}
