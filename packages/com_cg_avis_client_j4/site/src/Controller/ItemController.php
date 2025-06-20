<?php
/**
* CG Avis Client - Joomla Module
* Version			: 2.1.0
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

namespace ConseilGouz\Component\CGAvisClient\Site\Controller;

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class ItemController extends BaseController
{
    public $default_view = 'item';

    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;

        $safeurlparams = array('catid' => 'INT','id' => 'INT','cid' => 'ARRAY','year' => 'INT','month' => 'INT','limit' => 'INT','limitstart' => 'INT',
            'showall' => 'INT','return' => 'BASE64','filter' => 'STRING','filter_order' => 'CMD','filter_order_Dir' => 'CMD','filter-search' => 'STRING','print' => 'BOOLEAN','lang' => 'CMD');

        parent::display($cachable, $safeurlparams);

        return $this;
    }

    public function save()
    {

        $mainframe = Factory::getApplication();
        $data = new \StdClass();
        $model = $this->getModel('item');
        // Get the data from POST
        $post = $this->input->post->get('jform', array(), 'array');
        /// $post=JRequest::getVar('jform','','post','',JREQUEST_ALLOWRAW); // todo
        $data->category = $post['category'];
        $data->name = $post['name'];
        $data->firstname = $post['firstname'];
        $data->email = $post['email'];
        $data->zipcode = $post['zipcode'];
        $data->city = $post['city'];
        $data->rating = $post['rating'];
        $data->comment = $post['comment'];
        $data->state = 0;
        $data->created = date("Y-m-d h:i:s");
        $params = ComponentHelper::getParams('com_cgavisclient');
        if ($params->get('captcha') != '' || $params->get('captcha') != 0) {
            PluginHelper::importPlugin('captcha', $params->captcha);
            $res = $mainframe->triggerEvent('onCheckAnswer', array($this->input->get($params->captcha.'_response_field', '', 'post', '')));

            if (!$res[0]) {
                $message = Text::_("CGAVISCLIENT_CAPTCHA_MESSAGE");
                $link = Route::_('index.php?option=com_cgavisclient&view=item&layout=error&message='.$message, false);
                $this->setRedirect($link, $message);
                return false;
            }
        }
        // save to db
        $id = $model->save('#__cgavisclient', $data, '');
        // Send email to admin
        $MailFrom     = $mainframe->getCfg('mailfrom');
        $FromName     = $mainframe->getCfg('fromname');
        $rec = explode(',', $params->get('recipient_email'));

        // Prepare email body
        $prefix = Text::sprintf('ENQUIRY_TEXT', URI::base());
        $subject    = 'Nouvel Avis Client';
        $body	  = mb_convert_encoding(Text::_('CGAVISCLIENT_MESSAGE'), 'UTF-8', 'ISO-8859-1');
        $body     = sprintf($body, $data->name, $data->firstname, $data->zipcode, $data->city, $data->email, $data->comment);

        $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
        try {
            $mail->addRecipient($rec);
            $mail->setSender(array( $data->email, $data->name ));
            $mail->setSubject(mb_convert_encoding($subject, 'UTF-8', 'ISO-8859-1'));
            $mail->setBody($body);
            $mail->isHtml(true);
            $sent = $mail->Send();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $link = Route::_('index.php?option=com_cgavisclient&view=item&layout=error&message='.$message, false);
            $this->setRedirect($link, $message);
            return false;
        }

        $link = Route::_('index.php?option=com_cgavisclient&view=item&&layout=thankyou', false);
        $this->setRedirect($link, '');
    }

}
