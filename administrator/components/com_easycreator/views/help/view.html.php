<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Help
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package    EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewHelp extends JViewLegacy
{
    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        $task = JRequest::getCmd('task');

        switch($task)
        {
            case 'jhelp':
                $this->setLayout('jhelp');
                break;

            case 'help':
            default:
                break;
        }

        parent::display($tpl);

        EcrHtml::formEnd();
    }
}
